<?php
session_start();
require "../config/db.php";
require "../array/location.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

$cname = $website = $location = $description = $established_at = "";
$is_verified = "";

$cnameErr = $locationErr = $establishedErr = $logoErr = $verifiedErr = $websiteErr = "";

/* Fetch existing data */
$result = mysqli_query($conn, "SELECT * FROM company WHERE uid='$uid'");
$existing = mysqli_fetch_assoc($result);

if($existing){
    $cname = $existing['cname'];
    $website = $existing['website'];
    $location = $existing['location'];
    $description = $existing['description'];
    $established_at = $existing['established_at'];
    $is_verified = $existing['is_verified'];
    $logo = $existing['logo'];
}

/* FORM SUBMIT */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

   $cname = mysqli_real_escape_string($conn, trim($_POST["cname"] ?? ""));
$website = mysqli_real_escape_string($conn, trim($_POST["website"] ?? ""));
$location = mysqli_real_escape_string($conn, trim($_POST["location"] ?? ""));
$description = mysqli_real_escape_string($conn, trim($_POST["description"] ?? ""));
$established_at = mysqli_real_escape_string($conn, $_POST["established_at"] ?? "");
$is_verified = mysqli_real_escape_string($conn, $_POST["is_verified"] ?? "");

    if ($cname === "") $cnameErr = "Company name is required";
    if ($location === "") $locationErr = "Location is required";
    if ($established_at === "") $establishedErr = "Established date is required";
    if ($is_verified === "") $verifiedErr = "Please select verified status";

    if ($website !== "" && !filter_var($website, FILTER_VALIDATE_URL)) {
        $websiteErr = "Invalid website URL";
    }

    $logoName = "";

    if (!empty($_FILES["logo"]["name"])) {

        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);

        $logoName = "company_" . $uid . "_" . time() . ".jpg";
        $targetFile = $targetDir . $logoName;

        $check = getimagesize($_FILES["logo"]["tmp_name"]);

        if ($check === false) {
            $logoErr = "File is not a valid image";
        } elseif ($_FILES["logo"]["size"] > 2 * 1024 * 1024) {
            $logoErr = "Image must be less than 2MB";
        } else {

            list($width, $height) = getimagesize($_FILES["logo"]["tmp_name"]);

            $src = imagecreatefromstring(file_get_contents($_FILES["logo"]["tmp_name"]));
            $dst = imagecreatetruecolor(300, 300);

            imagecopyresampled($dst, $src, 0, 0, 0, 0,
                300, 300, $width, $height);

            imagejpeg($dst, $targetFile, 80);

            imagedestroy($src);
            imagedestroy($dst);
            $logo = $logoName;
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

        if($logoName != ""){
            $logoQuery = "logo='$logoName',";
        } else {
            $logoQuery = "";
        }

        $sql = "UPDATE company 
                SET cname='$cname',
                    $logoQuery
                    website='$website',
                    location='$location',
                    description='$description',
                    is_verified='$is_verified',
                    established_at='$established_at'
                WHERE uid='$uid'";

        if(mysqli_query($conn,$sql)){

            if($logoName != ""){
                mysqli_query($conn, "UPDATE users 
                                     SET p_image='$logoName'
                                     WHERE uid='$uid'");
                $_SESSION['p_image'] = $logoName;
            }

            $_SESSION['cname'] = $cname;
            $_SESSION['edit_success'] = "Profile updated successfully!";

            regenerateCSRFToken();

            header("Location: cdashboard.php");
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
<title>Career Craft | Edit Company Profile</title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">

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

        /* Make calendar icon white */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
       
</style>

</head>

<body class="bg-black px-4 py-6 overflow-x-hidden overflow-y-auto">

<?php include("../include/navbar.php"); ?>
<a href="cdashboard.php"
   class="inline-block mt-20 mb-4 text-yellow-400 text-sm hover:underline">
   ← Back to Dashboard
</a>


<div class="max-w-5xl mx-auto bg-[#0f0f0f] rounded-2xl shadow-2xl 
p-6 sm:p-8 border border-white/10 text-white mb-20">

<h2 class="text-2xl md:text-3xl font-bold text-[#D7AE27] mb-6 text-center">
Edit Company Profile
</h2>

<form method="POST" enctype="multipart/form-data"
class="grid grid-cols-1 md:grid-cols-2 gap-5" novalidate>

<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<!-- Company Logo -->
<div class="md:col-span-2 mb-4">
    <label class="block text-white mb-2">
        Company Logo
    </label>

    <div class="flex items-center gap-6">

        <!-- Preview -->
      <img id="logoPreview"
src="<?= !empty($logo) 
        ? 'uploads/'.$logo 
        : 'https://ui-avatars.com/api/?name='.urlencode($cname).'&background=D7AE27&color=000'; ?>"
class="w-24 h-24 rounded-full object-cover border-2 border-gray-300 shadow">
        <!-- File Input -->
        <input type="file"
               name="logo"
               id="logoInput"
               accept="image/*"
               class="text-white">
    </div>

    <p id="logoErr" class="error"><?= $logoErr ?? "" ?></p>
</div>

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

<!-- Location -->
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
class="input-field">
<p id="establishedErr" class="error"><?= $establishedErr ?></p>
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
<label>
<input type="radio" name="is_verified" value="1"
<?= $is_verified==="1"?"checked":"" ?>> Yes
</label>

<label>
<input type="radio" name="is_verified" value="0"
<?= $is_verified==="0"?"checked":"" ?>> No
</label>
</div>
<p id="verifiedErr" class="error"><?= $verifiedErr ?></p>
</div>

<!-- Submit -->
<div class="md:col-span-2 mt-6 flex justify-center">
<button class="bg-[#D7AE27] text-black px-8 py-2 rounded-md font-semibold hover:bg-yellow-400 transition">
Save Changes
</button>
</div>

</form>
</div>

<!-- JAVASCRIPT -->
<script>

    // LIVE VALIDATION
    const cnameField = document.getElementById("cname");
const cnameErr = document.getElementById("cnameErr");

const locationField = document.getElementById("locationField");
const locationErr = document.getElementById("locationErr");

const establishedField = document.getElementById("established_at");
const establishedErr = document.getElementById("establishedErr");

const websiteField = document.getElementById("website");
const websiteErr = document.getElementById("websiteErr");

const logoInput = document.getElementById("logoInput");
const preview = document.getElementById("logoPreview");
const logoErr = document.getElementById("logoErr");

const verifiedErr = document.getElementById("verifiedErr");

// Company Name
cnameField.addEventListener("input", function(){
    cnameErr.textContent =
        cnameField.value.trim() !== "" ? "" : "Company name is required";
});

// Location
locationField.addEventListener("change", function(){
    locationErr.textContent =
        locationField.value !== "" ? "" : "Location is required";
});

// Date
establishedField.addEventListener("change", function(){
    establishedErr.textContent =
        establishedField.value !== "" ? "" : "Established date is required";
});

// Website
websiteField.addEventListener("input", function(){
    const pattern = /^(https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-]*)*$/;
    websiteErr.textContent =
        websiteField.value.trim() === "" || pattern.test(websiteField.value)
        ? "" : "Invalid website URL";
});

// Verified radio
document.querySelectorAll('input[name="is_verified"]').forEach(radio => {
    radio.addEventListener("change", function(){
        verifiedErr.textContent = "";
    });
});

// Logo Preview
document.getElementById("logoInput").addEventListener("change", function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById("logoPreview").src = e.target.result;
    };
    reader.readAsDataURL(file);
});



</script>

<?php include("../include/footer.php"); ?>

</body>
</html>