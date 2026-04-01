<?php
session_start();
    require "../config/db.php";
    require "../authc/csrf.php";
require "../auth/session_check.php";

    $csrf_token = generateCSRFToken();
 
$uid = $_SESSION['uid'] ?? 0;

// get company id
$cid_query = mysqli_query($conn, "SELECT cid FROM company WHERE uid='$uid'");
$cid_row = mysqli_fetch_assoc($cid_query);
$cid = $cid_row['cid'] ?? 0;

// fetch seekers + saved status
$sql = "SELECT 
    js.sid,
    js.sname,
    js.education,
    js.experience,
    js.skillname,
    js.bio,
    js.profile_image,
    u.created_at,

    EXISTS(
        SELECT 1 FROM saved_candidate 
        WHERE saved_candidate.sid = js.sid 
        AND saved_candidate.cid = '$cid'
    ) AS saved

FROM job_seeker js
JOIN users u ON js.uid = u.uid
ORDER BY u.created_at DESC";

    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft |Applicants</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>
    <body class="bg-black text-white">
    <?php include("../include/navbar.php"); ?>

<a href="http://localhost/php_program/project/home.php"
   class="inline-block mt-20 text-yellow-400 text-sm hover:underline">
   ← Back
</a>
    <div class="max-w-7xl mx-auto px-6 py-10">

    
        <h2 class="text-3xl md:text-4xl font-semibold text-[#D7AE27] mb-16 text-center">
            Find
            <span class="relative inline-block text-white">
                Talent
                <span class="absolute left-0 top-full mt-6 w-full h-1 bg-[#D7AE27] rounded-sm"></span>
            </span>
        </h2>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">

    <?php while($row = mysqli_fetch_assoc($result)) { 

    $img = !empty($row['profile_image']) 
            ? "../seeker/uploads/".$row['profile_image'] 
            : "https://via.placeholder.com/100";

    $date = date("d M Y", strtotime($row['created_at']));
    ?>

    <!-- CARD -->
    <!-- CARD -->
<?php $saved = !empty($row['saved']); ?>

    <div class="bg-[#161616] p-6 rounded-2xl border border-gray-800 hover:border-yellow-400 transition-all duration-300 relative">


    <!-- SAVE ICON (NOW INSIDE CARD ✅) -->
<form method="POST" action="toggle_save_candidate.php" class="absolute top-4 right-4">
    <input type="hidden" name="sid" value="<?= $row['sid'] ?>">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <button type="submit" class="text-xl bg-transparent border-0">
        <i class="<?= $saved 
            ? 'fa-solid fa-bookmark text-yellow-400'
            : 'fa-regular fa-bookmark text-white hover:text-yellow-400' ?>">
        </i>
    </button>
</form>  

    <!-- TOP -->
    <div class="flex items-center gap-4">

        <img src="<?= $img ?>" 
            class="w-16 h-16 rounded-xl object-cover bg-white p-1">

        <div>
            <h3 class="text-lg font-semibold"><?= $row['sname'] ?></h3>
            <p class="text-gray-400 text-sm"><?= $row['education'] ?></p>
            <p class="text-gray-500 text-xs mt-1">Joined: <?= $date ?></p>
        </div>

    </div>

    <!-- TAGS -->
    <div class="flex flex-wrap gap-2 mt-4 text-xs">

        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
            <?= $row['experience'] ?>
        </span>

        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
            <?= $row['skillname'] ?>
        </span>

    </div>

    <!-- BIO -->
    <p class="text-gray-400 text-sm mt-4 leading-relaxed">
        <?= !empty($row['bio']) ? substr($row['bio'], 0, 80)."..." : "No description available" ?>
    </p>

    <!-- STATUS -->
    <div class="flex justify-between items-center mt-5">

        <span class="text-green-400 text-sm font-medium">
            ● Available for Hiring
        </span>

        <span class="text-gray-500 text-xs">
            Active
        </span>

    </div>

    <!-- BUTTON -->
    <a href="seeker_details.php?sid=<?= $row['sid'] ?>" 
    class="block mt-5 bg-[#D7AE27] text-black text-center py-2 rounded-xl 
            font-semibold hover:bg-yellow-500 transition">
        View Profile
    </a>

</div>

    <?php } ?>

    </div>

    </div>
<?php include("../include/footer.php");?>

    </body>
</html>