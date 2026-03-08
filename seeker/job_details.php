<?php
session_start();
include("../config/db.php");
require "../authc/csrf.php";

if(!isset($_SESSION['uid']) ){
    header("Location: ../auth/login.php");
    exit();
}
if(!isset($_GET['jid'])){
    header("Location: find_job.php");
    exit();
}

$jid = intval($_GET['jid']);
$sql = "SELECT job.*, 
        company.cname, 
        company.logo, 
        company.website, 
        company.location AS company_location,
        company.description AS company_desc
        FROM job
        JOIN company ON job.cid = company.cid
        WHERE job.jid=$jid";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result) == 0){
    echo "Job not found";
    exit();
}

$row = mysqli_fetch_assoc($result);

// Check saved
$saved = false;
if(isset($_SESSION['uid'])){
    $uid = $_SESSION['uid'];
  $check = mysqli_query($conn,
"SELECT 1 FROM saved_job 
 WHERE uid='$uid' AND jid='".$row['jid']."'");
    if(mysqli_num_rows($check) > 0){
        $saved = true;
    }
}
// Check if already applied
$applied = false;
$pendingTest = false;
$aid = 0;

$applyCheck = mysqli_query($conn,
"SELECT aid, score FROM application 
 WHERE uid='$uid' AND jid='".$row['jid']."' 
 LIMIT 1");

if(mysqli_num_rows($applyCheck) > 0){

    $appData = mysqli_fetch_assoc($applyCheck);
    $aid = $appData['aid'];

    if($appData['score'] == 0){
        $pendingTest = true; // Test not attempted
    }

    $applied = true; // Block second apply
}

// Logo fallback
$logo = !empty($row['logo']) 
        ? "../company/uploads/".$row['logo'] 
        : "https://via.placeholder.com/70";
?>
<!DOCTYPE html>
<html>
<head>
<title>Career craft | Job details</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">

</head>

<body class="bg-black text-white min-h-screen"> 
    <?php include("../include/navbar.php"); ?>
<a href="sdashboard.php"
   class="inline-block mt-20 text-yellow-400 text-sm hover:underline  ml-10">
   ← Back
</a>
<div class="max-w-4xl mx-auto bg-[#1a1a1a] p-8 rounded-2xl border border-gray-800 mt-5 mb-10">

<!-- Top Section -->
<div class="flex justify-between items-start">

<div class="flex items-center gap-4">
<img src="<?php echo $logo; ?>"
class="w-16 h-16 rounded-lg bg-white p-1">

<div>
<h2 class="text-2xl font-semibold"><?php echo htmlspecialchars($row['title']); ?></h2>
<p class="text-gray-400"><?php echo $row['cname']; ?></p>
</div>
</div>
<?php if(isset($_SESSION['uid'])) { ?>

<form method="POST" action="<?php echo $saved ? 'unsave_job.php' : 'save_job.php'; ?>" class="inline">

<input type="hidden" name="jid" value="<?php echo $row['jid']; ?>">
<input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

<button type="submit"
class="text-2xl <?php echo $saved ? 'text-yellow-400' : 'text-gray-400 hover:text-yellow-400'; ?>">

<i class="<?php echo $saved ? 'fa-solid' : 'fa-regular'; ?> fa-bookmark"></i>

</button>

</form>

<?php } ?>

</div>

<!-- Job Details -->
<div class="mt-8 space-y-3 text-gray-300">

<p><b>Location:</b> <?php echo $row['location']; ?></p>
<p><b>Experience:</b> <?php echo $row['experience_required']; ?></p>
<p><b>Job Type:</b> <?php echo $row['job_type']; ?></p>
<p><b>Work Mode:</b> <?php echo $row['work_mode']; ?></p>
<p><b>Salary:</b> ₹ <?php echo $row['salary']; ?></p>

<p class="text-red-400">
<b>Deadline:</b> <?php echo date("d M Y", strtotime($row['deadline'])); ?>
</p>

<p><b>Total Applicants:</b> <?php echo $row['applicant']; ?></p>

</div>

<hr class="my-8 border-gray-700">

<!-- Job Description -->
<h3 class="text-xl font-semibold mb-3">Job Description</h3>
<p class="text-gray-400 mb-6">
<?php echo nl2br($row['description']); ?>
</p>

<!-- Toggle Button -->
<button onclick="toggleCompany(this)"
class="mb-6 bg-gray-800 border border-yellow-400 text-yellow-400 px-5 py-2 rounded-lg hover:bg-yellow-400 hover:text-black transition">
View Company Details
</button>

<!-- Company Section (Hidden) -->
<div id="companySection" class="hidden">

<hr class="my-6 border-gray-700">

<h3 class="text-xl font-semibold mb-4">Company Details</h3>

<p class="text-gray-300 mb-2">
<b>Company Name:</b> <?php echo $row['cname']; ?>
</p>

<p class="text-gray-300 mb-2">
<b>Company Location:</b> <?php echo $row['company_location']; ?>
</p>

<?php if(!empty($row['website'])) { ?>
<p class="text-gray-300 mb-2">
<b>Website:</b> 
<a href="<?php echo $row['website']; ?>" target="_blank"
class="text-yellow-400 underline">
<?php echo $row['website']; ?>
</a>
</p>
<?php } ?>

<div class="mt-4">
<h4 class="text-lg font-semibold mb-2">About Company</h4>
<p class="text-gray-400">
<?php echo nl2br($row['company_desc']); ?>
</p>
</div>

</div>

<!-- Apply Button -->
<?php if(isset($_SESSION['uid'])): ?>

<?php if($pendingTest): ?>

<a href="test.php?aid=<?= $aid ?>"
class="inline-block bg-yellow-400 text-black px-6 py-2 rounded-lg font-medium hover:bg-yellow-500 mt-6">
Continue Aptitude Test
</a>

<?php elseif(!$applied): ?>

<a href="apply_job.php?jid=<?= $row['jid']; ?>"
class="inline-block bg-yellow-400 text-black px-6 py-2 rounded-lg font-medium hover:bg-yellow-500 mt-6">
Apply Now
</a>

<?php else: ?>

<button class="inline-block bg-gray-600 text-white px-6 py-2 rounded-lg mt-6 cursor-not-allowed">
Application Submitted
</button>

<?php endif; ?>

<?php endif; ?>

</div>

<script>
function toggleCompany(btn) {
    var section = document.getElementById("companySection");

    if(section.classList.contains("hidden")) {
        section.classList.remove("hidden");
        btn.innerText = "Hide Company Details";
    } else {
        section.classList.add("hidden");
        btn.innerText = "View Company Details";
    }
}
</script>

</body>
<?php include("../include/footer.php"); ?>

</html>