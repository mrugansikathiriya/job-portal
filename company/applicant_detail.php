<?php
session_start();
include("../config/db.php");
require "../authc/csrf.php";

/* SESSION + ROLE CHECK */
if(!isset($_SESSION['uid']) || $_SESSION['role']!='company'){
    header("Location: ../auth/login.php");
    exit();
}

if(!isset($_GET['aid'])){
    header("Location: total_applicant.php");
    exit();
}

$aid = intval($_GET['aid']);
$uid = $_SESSION['uid'];

/* VERIFY APPLICATION BELONGS TO COMPANY */
$check = mysqli_query($conn,"
SELECT application.aid
FROM application
JOIN job ON job.jid = application.jid
JOIN company ON company.cid = job.cid
WHERE application.aid='$aid' AND company.uid='$uid'
");

if(mysqli_num_rows($check)==0){
    die("Unauthorized access");
}

/* UPDATE STATUS */
if(isset($_POST['action'])){

    if(!validateCSRFToken($_POST['csrf_token'])){
        die("Invalid CSRF Token");
    }

    $status = mysqli_real_escape_string($conn,$_POST['action']);

    $check = "SELECT status FROM application WHERE aid='$aid'";
    $res = mysqli_query($conn,$check);
    $row = mysqli_fetch_assoc($res);

    if($row['status']=='pending'){
        mysqli_query($conn,"UPDATE application SET status='$status' WHERE aid='$aid'");
    }

    regenerateCSRFToken();

    header("Location: applicant_details.php?aid=".$aid);
    exit();
}

/* FETCH APPLICANT DETAILS */
$sql = "SELECT application.*, job_seeker.*, users.email, users.contact
FROM application
JOIN job_seeker ON job_seeker.sid = application.sid
JOIN users ON users.uid = job_seeker.uid
WHERE application.aid='$aid'";

$result = mysqli_query($conn,$sql);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Applicant Details</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png"></head>

<body class="bg-black text-white min-h-screen">
    <?php include("../include/navbar.php"); ?>

<a href="view_applicant.php"
   class="inline-block mt-20 text-yellow-400 text-sm hover:underline">
   ← Back
</a>
<div class="max-w-4xl mx-auto py-12 px-6">

<h2 class="text-3xl text-[#D7AE27] font-semibold mb-8 text-center">
Applicant Details
</h2>

<div class="bg-black/50 border border-gray-700 rounded-2xl p-8">

<div class="flex items-center gap-5 mb-6">

<?php if(!empty($data['profile_image'])): ?>

<img src="../seeker/uploads/<?= htmlspecialchars($data['profile_image']) ?>"
class="w-16 h-16 rounded-full object-cover border border-[#D7AE27]">

<?php else: ?>

<img src="https://ui-avatars.com/api/?name=<?= urlencode($data['sname']) ?>&background=D7AE27&color=000"
class="w-16 h-16 rounded-full border border-[#D7AE27]">

<?php endif; ?>

<div>
<h3 class="text-xl text-[#D7AE27] font-semibold">
<?= htmlspecialchars($data['sname']); ?>
</h3>

<p class="text-gray-400">
<?= htmlspecialchars($data['email']); ?>
</p>
</div>

</div>

<div class="grid grid-cols-2 gap-6 text-sm">

<div>
<span class="text-[#D7AE27]">Contact</span>
<p><?= htmlspecialchars($data['contact']); ?></p>
</div>

<div>
<span class="text-[#D7AE27]">Education</span>
<p><?= htmlspecialchars($data['education']); ?></p>
</div>

<div>
<span class="text-[#D7AE27]">Experience</span>
<p><?= htmlspecialchars($data['experience']); ?></p>
</div>

<div>
<span class="text-[#D7AE27]">Skills</span>
<p><?= htmlspecialchars($data['skillname']); ?></p>
</div>

<div>
<span class="text-[#D7AE27]">Bio</span>
<p><?= htmlspecialchars($data['bio']); ?></p>
</div>

<div>
<span class="text-[#D7AE27]">Score</span>
<p><?= htmlspecialchars($data['score']); ?></p>
</div>

<div>
<span class="text-[#D7AE27]">Current Status</span>

<p class="<?php
if($data['status']=='selected'){ echo 'text-green-400'; }
elseif($data['status']=='rejected'){ echo 'text-red-400'; }
else{ echo 'text-yellow-400'; }
?>">

<?= htmlspecialchars($data['status']); ?>

</p>
</div>

</div>

<div class="mt-6">

<a href="http://localhost/php_program/project/seeker/uploads/<?= htmlspecialchars($data['resume']) ?>"
target="_blank"
class="text-[#D7AE27] underline">
View Resume
</a>

</div>

<form method="POST" class="flex gap-4 mt-8">

<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<button type="submit" name="action" value="selected"
class="bg-green-500 hover:bg-green-600 px-6 py-2 rounded-lg disabled:opacity-50"
<?php if($data['status']!='pending'){ echo 'disabled'; } ?>>
Accept
</button>

<button type="submit" name="action" value="rejected"
class="bg-red-500 hover:bg-red-600 px-6 py-2 rounded-lg disabled:opacity-50"
<?php if($data['status']!='pending'){ echo 'disabled'; } ?>>
Reject
</button>

</form>

</div>

</div>

</body>
<?php include("../include/footer.php"); ?>

</html>