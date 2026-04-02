<?php
session_start();
require "../config/db.php";
require "../auth/session_check.php"; // login check

if($_SESSION['role'] != 'seeker'){   // role check
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

/* ================= FETCH DATA ================= */
$sql = "SELECT 
        u.uname, u.email, u.contact, u.status, u.created_at, u.role,
        s.sname, s.education, s.experience, s.skillname, s.bio, 
        s.profile_image, s.birthdate
        FROM users u
        LEFT JOIN job_seeker s ON u.uid = s.uid
        WHERE u.uid='$uid'
        LIMIT 1";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

/* ================= PROFILE IMAGE ================= */
$image = !empty($data['profile_image']) 
    ? "../seeker/uploads/".$data['profile_image'] 
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

<a href="sdashboard.php" class="inline-block mt-20 text-yellow-400 ml-10 hover:underline">
← Back
</a>

<div class="max-w-5xl mx-auto mt-6 mb-12 px-6">

<div class="bg-[#161616] rounded-2xl border border-gray-800 p-8 shadow-xl">

<!-- ================= HEADER ================= -->
<div class="flex flex-col sm:flex-row items-center gap-6 mb-8">

    <img src="<?= $image ?>" 
    class="w-24 h-24 rounded-full bg-white p-1 border border-gray-600 object-cover">

    <div class="text-center sm:text-left">
        <h2 class="text-2xl font-semibold">
            <?= htmlspecialchars($data['sname'] ?: $data['uname']) ?>
        </h2>
        <p class="text-gray-400"><?= htmlspecialchars($data['email']) ?></p>
               <p class="text-yellow-400 capitalize"><?= $data['role'] ?></p>

    </div>

</div>

<hr class="border-gray-700 mb-6">

<!-- ================= ACCOUNT DETAILS ================= -->
<h3 class="text-xl font-semibold mb-4 text-yellow-400 flex items-center gap-2">
<i class="fa-solid fa-user"></i> Account Details
</h3>

<div class="grid sm:grid-cols-2 gap-6 mb-8">

<div>
<p class="text-gray-400 text-sm">Username</p>
<p class="font-semibold"><?= htmlspecialchars($data['uname']) ?></p>
</div>

<div>
<p class="text-gray-400 text-sm">Contact</p>
<p class="font-semibold"><?= htmlspecialchars($data['contact'] ?: 'Not Provided') ?></p>
</div>

<div>
<p class="text-gray-400 text-sm">Status</p>
<p class="<?= ($data['status']=='active') ? 'text-green-400' : 'text-red-400' ?>">
<?= ucfirst($data['status']) ?>
</p>
</div>

<div>
<p class="text-gray-400 text-sm">Joined</p>
<p class="font-semibold"><?= date("d M Y", strtotime($data['created_at'])) ?></p>
</div>

</div>

<hr class="border-gray-700 mb-6">

<!-- ================= SEEKER DETAILS ================= -->
<h3 class="text-xl font-semibold mb-4 text-yellow-400 flex items-center gap-2">
<i class="fa-solid fa-id-card"></i> Profile Details
</h3>

<div class="grid sm:grid-cols-2 gap-6">

<div>
<p class="text-gray-400 text-sm">Full Name</p>
<p class="font-semibold"><?= htmlspecialchars($data['sname'] ?: 'N/A') ?></p>
</div>

<div>
<p class="text-gray-400 text-sm">Birthdate</p>
<p class="font-semibold">
<?= !empty($data['birthdate']) ? date("d M Y", strtotime($data['birthdate'])) : 'N/A' ?>
</p>
</div>

<div>
<p class="text-gray-400 text-sm">Education</p>
<p class="font-semibold"><?= htmlspecialchars($data['education'] ?: 'Not Provided') ?></p>
</div>

<div>
<p class="text-gray-400 text-sm">Experience</p>
<p class="font-semibold"><?= htmlspecialchars($data['experience'] ?: '0 Years') ?></p>
</div>

<div>
<p class="text-gray-400 text-sm">Skills</p>
<p class="font-semibold"><?= htmlspecialchars($data['skillname'] ?: 'Not Provided') ?></p>
</div>

</div>

<!-- BIO -->
<div class="mt-8">
<p class="text-gray-400 text-sm mb-2">Bio</p>
<p class="text-gray-300 leading-relaxed">
<?= !empty($data['bio']) ? nl2br(htmlspecialchars($data['bio'])) : 'No bio available' ?>
</p>
</div>

<!-- ================= ACTION BUTTONS ================= -->
<div class="flex flex-col sm:flex-row gap-4 mt-10">

<a href="sedit_profile.php" 
class="flex-1 text-center bg-yellow-400 text-black py-3 rounded-lg font-semibold hover:bg-yellow-500 transition">
✏ Edit Profile
</a>

<a href="sdelete_account.php" 
onclick="return confirm('Are you sure you want to delete your account?')"
class="flex-1 text-center bg-red-600 py-3 rounded-lg font-semibold hover:bg-red-700 transition">
Delete Account
</a>

</div>

</div>
</div>

<?php include("../include/footer.php"); ?>

</body>
</html>