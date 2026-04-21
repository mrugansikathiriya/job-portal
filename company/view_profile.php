<?php
session_start();
require "../config/db.php";
require "../auth/session_check.php"; // login check

if($_SESSION['role'] != 'company'){   // role check
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

/* ================= FETCH DATA ================= */
$sql = "SELECT u.*, c.*
        FROM users u
        LEFT JOIN company c ON u.uid = c.uid
        WHERE u.uid='$uid'";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

// ✅ ONLY COMPANY LOGO
$logo = !empty($data['logo']) 
? "../company/uploads/".$data['logo'] 
: "https://via.placeholder.com/120";
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Career Craft | My Profile</title>
        <link href="../dist/styles.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="icon" href="../image/logo3.jpg">
    </head>

    <body class="bg-black text-white min-h-screen">

        <?php include("../include/navbar.php"); ?>

        <a href="cdashboard.php" class="inline-block mt-20 text-yellow-400 ml-10">
        ← Back
        </a>

        <div class="max-w-5xl mx-auto mt-6 mb-10 px-6">

        <div class="bg-[#161616] rounded-2xl border border-gray-800 p-8 shadow-xl">

        <!-- ================= HEADER ================= -->
        <div class="flex flex-col md:flex-row items-center gap-6 mb-8">

            <!-- ✅ ONLY ONE LOGO -->
            <img src="<?= $logo ?>" 
            class="w-24 h-24 rounded-full bg-white p-1 border border-gray-700 object-cover">

            <div class="text-center md:text-left">
                <h2 class="text-2xl font-semibold"><?= $data['cname'] ?: 'Company Name' ?></h2>
                <p class="text-gray-400"><?= $data['email'] ?></p>
                <p class="text-yellow-400 capitalize"><?= $data['role'] ?></p>
            </div>

        </div>

        <hr class="border-gray-700 mb-6">

        <!-- ================= USER INFO ================= -->
        <h3 class="text-xl font-semibold mb-4 text-yellow-400">Account Details</h3>

        <div class="grid md:grid-cols-2 gap-6 mb-8">

        <div>
        <p class="text-gray-400 text-sm">Username</p>
        <p><?= $data['uname'] ?></p>
        </div>

        <div>
        <p class="text-gray-400 text-sm">Contact</p>
        <p><?= $data['contact'] ?: 'Not Provided' ?></p>
        </div>

        <div>
        <p class="text-gray-400 text-sm">Status</p>
        <p class="text-green-400"><?= $data['status'] ?></p>
        </div>

        <div>
        <p class="text-gray-400 text-sm">Joined</p>
        <p><?= date("d M Y", strtotime($data['created_at'])) ?></p>
        </div>

        </div>

        <hr class="border-gray-700 mb-6">

        <!-- ================= COMPANY INFO ================= -->
        <h3 class="text-xl font-semibold mb-4 text-yellow-400">Company Details</h3>

        <div class="grid md:grid-cols-2 gap-6">

        <div>
        <p class="text-gray-400 text-sm">Company Name</p>
        <p><?= $data['cname'] ?: 'N/A' ?></p>
        </div>

        <div>
        <p class="text-gray-400 text-sm">Location</p>
        <p><?= $data['location'] ?: 'Not Provided' ?></p>
        </div>

        <div>
        <p class="text-gray-400 text-sm">Website</p>
        <?php if(!empty($data['website'])){ ?>
        <a href="<?= $data['website'] ?>" target="_blank" class="text-yellow-400 underline">
        <?= $data['website'] ?>
        </a>
        <?php } else { ?>
        <p>Not Provided</p>
        <?php } ?>
        </div>

        <div>
        <p class="text-gray-400 text-sm">Established</p>
        <p><?= $data['established_at'] ?: 'N/A' ?></p>
        </div>

        <div>
        <p class="text-gray-400 text-sm">Verification</p>
        <p class="<?= $data['is_verified'] ? 'text-green-400' : 'text-red-400' ?>">
        <?= $data['is_verified'] ? 'Verified' : 'Not Verified' ?>
        </p>
        </div>

        </div>

        <!-- DESCRIPTION -->
        <div class="mt-6">
        <p class="text-gray-400 text-sm mb-2">Company Description</p>
        <p class="text-gray-300 leading-relaxed">
        <?= nl2br($data['description'] ?: 'No description available') ?>
        </p>
        </div>

        <!-- ================= ACTION ================= -->
        <div class="flex gap-4 mt-8">

        <a href="cedit_profile.php" 
        class="flex-1 text-center bg-yellow-400 text-black py-2 rounded-lg hover:bg-yellow-500 transition">
        Edit Profile
        </a>

        <a href="cdelete_account.php" 
        onclick="return confirm('Are you sure you want to delete your account?');"
        class="flex-1 text-center bg-red-600 py-2 rounded-lg hover:bg-red-700 transition">
        Delete Account
        </a>

        </div>

        </div>
        </div>

        <?php include("../include/footer.php"); ?>

    </body>
</html>