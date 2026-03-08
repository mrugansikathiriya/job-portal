<?php
session_start();
include("../config/db.php");

/* SESSION + ROLE CHECK */
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

/* JOB ID CHECK */
if(!isset($_GET['jid'])){
    header("Location: total_applicant.php");
    exit();
}

$jid = intval($_GET['jid']);
$uid = $_SESSION['uid'];

/* VERIFY JOB BELONGS TO THIS COMPANY */
$check = mysqli_query($conn,"
SELECT job.jid 
FROM job
JOIN company ON company.cid = job.cid
WHERE job.jid='$jid' AND company.uid='$uid'
");

if(mysqli_num_rows($check)==0){
    die("Unauthorized access");
}

/* GET APPLICANTS */
$sql = "SELECT application.*, job_seeker.sname, job_seeker.profile_image,
users.email, users.contact
FROM application
JOIN job_seeker ON job_seeker.sid = application.sid
JOIN users ON users.uid = job_seeker.uid
WHERE application.jid='$jid'
ORDER BY application.aid DESC";

$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | View Applicants</title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white min-h-screen">
    <?php include("../include/navbar.php"); ?>

<a href="total_applicant.php"
   class="inline-block mt-20 text-yellow-400 text-sm hover:underline">
   ← Back
</a>
<div class="max-w-6xl mx-auto py-12 px-6">

<h2 class="text-3xl font-semibold text-center mb-10 text-[#D7AE27]">
Applicants
</h2>

<div class="bg-[#1a1a1a] rounded-xl border border-gray-800 p-6">

<?php if(mysqli_num_rows($result) == 0){ ?>

<p class="text-center text-gray-400">
No applicants found for this job.
</p>

<?php } ?>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<div class="bg-black/50 border border-gray-700 rounded-2xl p-6 mb-6 hover:border-yellow-400 transition">

<div class="flex items-center justify-between">

<div class="flex items-center gap-4">

<!-- Avatar -->
<?php if(!empty($row['profile_image'])): ?>

<img src="../seeker/uploads/<?= htmlspecialchars($row['profile_image']); ?>"
class="w-14 h-14 rounded-full object-cover border border-[#D7AE27]">

<?php else: ?>

<img src="https://ui-avatars.com/api/?name=<?= urlencode($row['sname']); ?>&background=D7AE27&color=000"
class="w-14 h-14 rounded-full border border-[#D7AE27]">

<?php endif; ?>

<!-- Applicant Info -->
<div>
<h3 class="text-lg font-semibold">
<?= htmlspecialchars($row['sname']); ?>
</h3>

<p class="text-gray-400 text-sm">
<?= htmlspecialchars($row['email']); ?>
</p>
</div>

</div>

</div>

<!-- Info Tags -->
<div class="flex flex-wrap gap-3 mt-4">

<span class="bg-yellow-500/20 text-[#D7AE27] px-3 py-1 rounded-full text-xs">
Contact : <?= htmlspecialchars($row['contact']); ?>
</span>

<span class="bg-yellow-500/20 text-[#D7AE27] px-3 py-1 rounded-full text-xs">
Score : <?= htmlspecialchars($row['score']); ?>
</span>

<span class="bg-yellow-500/20 text-[#D7AE27] px-3 py-1 rounded-full text-xs">
Status : <?= htmlspecialchars($row['status']); ?>
</span>

</div>

<!-- Actions -->
<div class="mt-4 flex items-center justify-between">

<a href="http://localhost/php_program/project/seeker/uploads/<?= htmlspecialchars($row['resume']); ?>" 
target="_blank"
class="text-[#D7AE27] underline text-sm">
View Resume
</a>

<a href="applicant_detail.php?aid=<?= $row['aid']; ?>" 
class="bg-yellow-400 hover:bg-yellow-500 text-black px-5 py-2 rounded-lg font-medium transition">
View Details
</a>

</div>

</div>

<?php } ?>

</div>

</div>

</body>
<?php include("../include/footer.php"); ?>

</html>