<?php
session_start();
include("../config/db.php");
require "../authc/csrf.php";
$csrf_token = generateCSRFToken();

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'seeker'){
    header("Location: ../auth/login.php");
    exit();
}

if($_SESSION['is_completed'] == 0){
    header("Location: seeker_profile.php");
    exit();
}

$uid = $_SESSION['uid'];

// Get seeker details
$seeker = mysqli_query($conn, "SELECT * FROM job_seeker WHERE uid='$uid'");
$sdata = mysqli_fetch_assoc($seeker);

// Count applied jobs
$applied_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM application WHERE uid='$uid'");
$adata = mysqli_fetch_assoc($applied_count);

// Optional: Count saved jobs
$saved_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM saved_job WHERE uid='$uid'");
$sdata_count = mysqli_fetch_assoc($saved_count);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Career Craft | Seeker Dashboard</title>
    <link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>
<body class="bg-black text-white min-h-screen">

<?php include("../include/navbar.php"); ?>

<?php if(isset($_SESSION['login_success'])): ?>
<div id="flashMessage"
     class="fixed top-15 right-5 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 
            flex items-center justify-between gap-4 min-w-[280px] 
            transition-opacity duration-500">

    <span><?= $_SESSION['login_success']; ?></span>

    <!-- Close Button -->
    <button onclick="closeFlash()"
            class="text-white text-xl font-bold hover:text-gray-200 leading-none">
        &times;
    </button>
</div>
<?php unset($_SESSION['login_success']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['profile_success'])): ?>
<div id="flashMessage"
     class="fixed top-20 right-5 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 
            flex items-center justify-between gap-4 min-w-[280px] transition-opacity duration-500">
    <span><?= $_SESSION['profile_success']; ?></span>
    <button onclick="closeFlash()" class="text-white text-xl font-bold hover:text-gray-200 leading-none">&times;</button>
</div>
<?php unset($_SESSION['profile_success']); ?>
<?php endif; ?>

<!-- SUCCESS TOAST -->
<?php if(isset($_SESSION['edit_success'])): ?>
<div id="flashMessage"
     class="fixed top-20 right-5 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 
            flex items-center justify-between gap-4 min-w-[280px] 
            transition-opacity duration-500">

    <span><?= $_SESSION['edit_success']; ?></span>

    <button onclick="closeFlash()"
            class="text-white text-xl font-bold hover:text-gray-200 leading-none">
        &times;
    </button>
</div>
<?php unset($_SESSION['edit_success']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['success_msg'])): ?>
<div id="flashMessage"
     class="fixed top-15 right-5 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 
            flex items-center justify-between gap-4 min-w-[280px] 
            transition-opacity duration-500">

    <span><?= $_SESSION['success_msg']; ?></span>

    <!-- Close Button -->
    <button onclick="closeFlash()"
            class="text-white text-xl font-bold hover:text-gray-200 leading-none">
        &times;
    </button>
</div>
<?php unset($_SESSION['success_msg']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['fail_msg'])): ?>
<div id="flashMessage"
     class="fixed top-15 right-5 bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 
            flex items-center justify-between gap-4 min-w-[280px] 
            transition-opacity duration-500">

    <span><?= $_SESSION['fail_msg']; ?></span>

    <!-- Close Button -->
    <button onclick="closeFlash()"
            class="text-white text-xl font-bold hover:text-gray-200 leading-none">
        &times;
    </button>
</div>
<?php unset($_SESSION['fail_msg']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-4 py-10 mt-20">

    <!-- Seeker Info Card -->
    <div class="bg-[#0f0f0f] border border-white/10 rounded-2xl shadow-xl p-8 mb-10 flex flex-col sm:flex-row items-center gap-6">

      <?php if(!empty($sdata['profile_image'])): ?>
        <img src="uploads/<?= $sdata['profile_image']; ?>" 
             class="w-28 h-28 rounded-xl object-cover border border-[#D7AE27]">
      <?php else: ?>
        <img src="https://ui-avatars.com/api/?name=<?= urlencode($sdata['sname']) ?>&background=D7AE27&color=000"
             class="w-28 h-28 rounded-xl border border-[#D7AE27]">
      <?php endif; ?>

        <div class="text-center sm:text-left">
            <h2 class="text-3xl font-bold text-[#D7AE27]"><?= $sdata['sname']; ?></h2>
            <p class="text-white/70 mt-2">🎓 <?= $sdata['education']; ?></p>
            <p class="text-white/70 mt-1">💼 <?= $sdata['experience']; ?></p>
            <p class="text-[#D7AE27] mt-1">⚡ Skills: <?= $sdata['skillname']; ?></p>
        </div>

        <div class="mt-4 sm:mt-0 sm:ml-auto">
            <a href="sedit_profile.php"
               class="bg-[#D7AE27] text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition duration-300">
               ✏ Edit Profile
            </a>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- Total Applications -->
         <a href="find_job.php?applied=1" class="block">
        <div class="bg-[#0f0f0f] border border-white/10 rounded-2xl shadow-xl p-8 text-center hover:shadow-2xl transition duration-300">
            <h3 class="text-5xl font-bold text-[#D7AE27]"><?= $adata['total']; ?></h3>
            <p class="text-white/70 mt-3">Total Jobs Applied</p>
        </div>

        <!-- Total Saved Jobs -->
<a href="find_job.php?saved=1" class="block">        
    <div class="bg-[#0f0f0f] border border-white/10 rounded-2xl shadow-xl p-8 text-center hover:shadow-2xl transition duration-300">
            <h3 class="text-5xl font-bold text-[#D7AE27]"><?= $sdata_count['total']; ?></h3>
            <p class="text-white/70 mt-3">Saved Jobs</p>
        </div>

        <!-- Browse Jobs -->
        <div class="bg-[#0f0f0f] border border-white/10 rounded-2xl shadow-xl p-8 flex items-center justify-center hover:shadow-2xl transition duration-300">
            <a href="http://localhost/php_program/project/seeker/find_job.php"
               class="bg-[#D7AE27] text-black px-8 py-3 rounded-xl font-bold hover:scale-105 transition duration-300">
               🔍 Browse Jobs
            </a>
        </div>

    </div>

</div>

<script>
function closeFlash() {
    const flash = document.getElementById("flashMessage");
    if (flash) {
        flash.style.opacity = "0";
        setTimeout(() => flash.remove(), 500);
    }
}

// Auto hide after 1 minute
setTimeout(closeFlash, 60000);
</script>

</body>
<?php include("../include/footer.php"); ?>

</html>