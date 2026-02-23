    <?php
    require "../config/db.php";
    require "../array/location.php";
    session_start();

    $uid = $_SESSION['uid'] ?? 1;

    $cname = $website = $location = $description = $established_at = "";
    $is_verified = "";

    $cnameErr = $locationErr = $establishedErr = $logoErr = $verifiedErr = $websiteErr = "";
    $success = false;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $cname = trim($_POST["cname"] ?? "");
        $website = trim($_POST["website"] ?? "");
        $location = trim($_POST["location"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $established_at = $_POST["established_at"] ?? "";
        $is_verified = $_POST["is_verified"] ?? "";

        if ($cname === "") $cnameErr = "Company name is required";
        if ($location === "") $locationErr = "Location is required";
        if ($established_at === "") $establishedErr = "Established date is required";
        if ($is_verified === "") $verifiedErr = "Please select verified status";

        if ($website !== "" && !filter_var($website, FILTER_VALIDATE_URL)) {
            $websiteErr = "Invalid website URL";
        }

        if (empty($_FILES["logo"]["name"])) {
            $logoErr = "Company logo is required";
        } else {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir);

            $logoName = time() . "_" . basename($_FILES["logo"]["name"]);
            $targetFile = $targetDir . $logoName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $allowed = ["jpg","jpeg","png"];

            if (!in_array($imageFileType, $allowed)) {
                $logoErr = "Only JPG, JPEG & PNG allowed";
            } else {
                move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile);
            }
        }

        if (
            $cnameErr=="" &&
            $locationErr=="" &&
            $establishedErr=="" &&
            $logoErr=="" &&
            $verifiedErr=="" &&
            $websiteErr==""
        ) {

            $sql = "INSERT INTO company 
            (uid, cname, logo, website, location, description, is_verified, established_at)
            VALUES (
            '$uid','$cname','$logoName','$website',
            '$location','$description','$is_verified','$established_at'
            )";

         if(mysqli_query($conn,$sql)){

    // ✅ UPDATE users table (VERY IMPORTANT)
    mysqli_query($conn, "
        UPDATE users 
        SET is_completed = 1 
        WHERE uid = '$uid'
    ");

    // Optional: update session also
    $_SESSION['is_completed'] = 1;

    $success = true;

    // Clear form values
    $cname = $website = $location = $description = $established_at = "";
    $is_verified = "";

    // ✅ Redirect to dashboard
    header("Location:http://localhost/php_program/project/home.php");
    exit;
}
        }
    }
    ?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Craft | Registration</title>
    <link href="../dist/styles.css" rel="stylesheet">

        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="icon" href="../image/logo3.jpg" type="image/png">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
        /* Make calendar icon white */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
        </style>
    </head>

    <body class="bg-black px-4 py-6 overflow-x-hidden overflow-y-auto">

    <?php if($success): ?>
    <div id="successToast"
    class="fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg transition-opacity duration-500">
    Company Registered Successfully
    </div>
    <?php endif; ?>
    <?php include("../include/navbar.php");?>
     <a href="http://localhost/php_program/project/auth/login.php" class="absolute left-4 top-4  mt-20 text-yellow-400 text-sm hover:underline">← Back</a>

    <div class="max-w-5xl mx-auto bg-[#0f0f0f] rounded-2xl shadow-2xl 
    p-6 sm:p-8 border border-white/10 text-white mt-20 mb-10">

    <h2 class="text-2xl md:text-3xl font-bold text-[#D7AE27] mb-6 text-center">
    Company Registration
    </h2>

    <form method="POST" enctype="multipart/form-data"
    class="grid grid-cols-1 md:grid-cols-2 gap-5" novalidate>

    <!-- Company Name -->
    <div>
        <label>Company Name <span class="text-red-500">*</span></label>
        <input id="cname" name="cname"
        value="<?= htmlspecialchars($cname) ?>"
        class="input-field">
        <p id="cnameErr" class="error"><?= $cnameErr ?></p>
    </div>

    <!-- Website -->
    <div>
        <label>Website</label>
        <input id="website" name="website"
        value="<?= htmlspecialchars($website) ?>"
        class="input-field">
        <p id="websiteErr" class="error"><?= $websiteErr ?></p>
    </div>

    <!-- ✅ UPDATED LOCATION DROPDOWN -->
    <div>
    <label>Location <span class="text-red-500">*</span></label>
        <select id="locationField" name="location" class="input-field">
        <option value="">Select Location</option>
        <?php foreach($locationList as $loc): ?>
        <option value="<?= $loc ?>" <?= ($location==$loc)?"selected":"" ?>>
        <?= $loc ?>
        </option>
        <?php endforeach; ?>
        </select>
        <p id="locationErr" class="error"><?= $locationErr ?></p>
    </div>

    <!-- Established Date -->
    <div>
        <label>Established Date <span class="text-red-500">*</span></label>
        <input type="date" id="established_at" name="established_at"
        value="<?= htmlspecialchars($established_at) ?>"
        placeholder="Select Established Date"
        class="input-field">
        <p id="establishedErr" class="error"><?= $establishedErr ?></p>
    </div>

    <!-- Logo -->
    <div class="md:col-span-2">
            <label>Company Logo <span class="text-red-500">*</span></label>
            <input type="file" id="logo" name="logo" class="input-field">
            <p id="logoErr" class="error"><?= $logoErr ?></p>
    </div>

    <!-- Description -->
    <div class="md:col-span-2">
        <label>Description</label>
        <textarea name="description" rows="4"
        class="input-field resize-none"><?= htmlspecialchars($description) ?></textarea>
    </div>

    <!-- Verified -->
    <div class="md:col-span-2">
        <label>Please select Verified or Not? <span class="text-red-500">*</span></label>
        <div class="flex gap-6 mt-2">
        <label><input type="radio" name="is_verified" value="1" <?= $is_verified==="1"?"checked":"" ?>> Yes</label>
        <label><input type="radio" name="is_verified" value="0" <?= $is_verified==="0"?"checked":"" ?>> No</label>
        </div>
        <p id="verifiedErr" class="error"><?= $verifiedErr ?></p>
    </div>

    <div class="md:col-span-2 mt-6 flex justify-center">
        <button class="bg-[#D7AE27] text-black px-8 py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
        Register
        </button>
    </div>

    </form>
</div>

<style>
    .input-field{
    width:100%;
    background:black;
    border:1px solid rgba(255,255,255,0.2);
    border-radius:6px;
    padding:10px;
    margin-top:5px;
    color:white;
    }
    .error{
    color:#f87171;
    font-size:14px;
    margin-top:4px;
    }
</style>


    <script>
    flatpickr("#established_at", {
        dateFormat: "Y-m-d",
        maxDate: "today"
    });

    // LIVE VALIDATION
    const cnameField = document.getElementById("cname");
    const cnameErr = document.getElementById("cnameErr");

    const locationField = document.getElementById("locationField");
    const locationErr = document.getElementById("locationErr");

    const establishedField = document.getElementById("established_at");
    const establishedErr = document.getElementById("establishedErr");

    const websiteField = document.getElementById("website");
    const websiteErr = document.getElementById("websiteErr");

    const logoField = document.getElementById("logo");
    const logoErr = document.getElementById("logoErr");

    const verifiedErr = document.getElementById("verifiedErr");

    cnameField.addEventListener("input", function(){
        cnameErr.textContent =
            cnameField.value.trim() !== "" ? "" : "Company name is required";
    });

    locationField.addEventListener("change", function(){
        locationErr.textContent =
            locationField.value !== "" ? "" : "Location is required";
    });

    establishedField.addEventListener("change", function(){
        establishedErr.textContent =
            establishedField.value !== "" ? "" : "Established date is required";
    });

    websiteField.addEventListener("input", function(){
        const pattern = /^(https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-]*)*$/;
        websiteErr.textContent =
            websiteField.value.trim() === "" || pattern.test(websiteField.value)
            ? "" : "Invalid website URL";
    });

    logoField.addEventListener("change", function(){
        logoErr.textContent =
            logoField.files.length > 0 ? "" : "Company logo is required";
    });

    document.querySelectorAll('input[name="is_verified"]').forEach(radio => {
        radio.addEventListener("change", function(){
            verifiedErr.textContent = "";
        });
    });

    setTimeout(() => {
    const toast = document.getElementById("successToast");
    if(toast){
        toast.style.opacity = "0";
        setTimeout(() => toast.remove(), 500);
    }
    },3000);

    </script>
    <?php include("../include/footer.php");?>

    </body>

    </html>