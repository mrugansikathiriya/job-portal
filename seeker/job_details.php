<?php
session_start();
include("../config/db.php");
require "../authc/csrf.php";
require "../auth/session_check.php";

if(!isset($_SESSION['uid'])){
    header("Location: ../auth/login.php");
    exit();
}

if(!isset($_GET['jid'])){
    header("Location: find_job.php");
    exit();
}

$uid = $_SESSION['uid'];
$jid = intval($_GET['jid']);

/* ================= JOB ================= */
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

/* ================= SAVE ================= */
$saved = false;

$check = mysqli_query($conn,
"SELECT 1 FROM saved_job 
 WHERE uid='$uid' AND jid='$jid'");

if(mysqli_num_rows($check) > 0){
    $saved = true;
}

/* ================= APPLY ================= */

$applyCheck = mysqli_query($conn,
"SELECT aid, score, status FROM application 
 WHERE uid='$uid' AND jid='$jid' 
 ORDER BY aid DESC 
 LIMIT 1");
$applied = false;
$pendingTest = false;
$canReapply = false;
$aid = 0;

if(mysqli_num_rows($applyCheck) > 0){
    $appData = mysqli_fetch_assoc($applyCheck);

    $status = strtolower($appData['status']);
    $aid = $appData['aid'];

    // ❌ REJECTED → FULLY BLOCK
    if($status == 'rejected'){
        $applied = true;      // treat as already applied
        $pendingTest = false; // no test
        $canReapply = false;  // no reapply
    }

    // ✅ WITHDRAWN → allow reapply
    elseif($status == 'withdrawn'){
        $canReapply = true;
        $applied = false;
    }

    // ✅ NORMAL FLOW
    else {
        $applied = true;

        if($appData['score'] == 0){
            $pendingTest = true;
        }
    }
}

/* ================= JOB LIST ================= */
$list_sql = "SELECT job.*, company.cname, company.logo
FROM job
JOIN company ON job.cid = company.cid";

$list_result = mysqli_query($conn,$list_sql);

$logo = !empty($row['logo']) 
? "../company/uploads/".$row['logo'] 
: "https://via.placeholder.com/70";
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Job Details</title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white min-h-screen">

<?php include("../include/navbar.php"); ?>

<a href="find_job.php"
class="inline-block mt-20 text-yellow-400 text-sm hover:underline ml-10">
← Back
</a>

<div class="w-full mt-5 mb-10 grid grid-cols-1 lg:grid-cols-10 gap-6 px-6">

<!-- ================= LEFT ================= -->
<div class="lg:col-span-7 space-y-6">

<!-- JOB CARD -->
<div class="bg-[#1a1a1a] p-8 rounded-2xl border border-gray-800 shadow-xl">

<!-- HEADER -->
<div class="flex justify-between items-center mb-6">

<div class="flex items-center gap-4">
<img src="<?= $logo ?>" class="w-17 h-17 rounded-xl bg-white p-1 shadow">

<div>
<h2 class="text-2xl font-bold"><?= htmlspecialchars($row['title']) ?></h2>
<p class="text-gray-400"><?= $row['cname'] ?></p>
</div>
</div>

<!-- SAVE -->
<form method="POST" action="<?= $saved ? 'unsave_job.php' : 'save_job.php'; ?>">
<input type="hidden" name="jid" value="<?= $jid ?>">
<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<button class="text-2xl <?= $saved ? 'text-yellow-400' : 'text-gray-400' ?>">
<i class="<?= $saved ? 'fa-solid' : 'fa-regular'; ?> fa-bookmark"></i>
</button>
</form>

</div>

<!-- INFO GRID -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 text-center mb-10 mt-10">

<div>
<i class="fa-solid fa-location-dot text-yellow-400 mb-2"></i>
<p class="text-xs text-gray-400">Location</p>
<p><?= $row['location'] ?></p>
</div>

<div>
<i class="fa-solid fa-briefcase text-yellow-400 mb-2"></i>
<p class="text-xs text-gray-400">Experience</p>
<p><?= $row['experience_required'] ?>  Years</p>
</div>

<div>
<i class="fa-solid fa-indian-rupee-sign text-yellow-400 mb-2"></i>
<p class="text-xs text-gray-400">Salary</p>
<p>₹ <?= $row['salary'] ?>  LPA</p>
</div>

<div>
<i class="fa-solid fa-bolt text-yellow-400 mb-2"></i>
<p class="text-xs text-gray-400">Type</p>
<p><?= $row['job_type'] ?></p>
</div>

</div>

<!-- TAGS -->
<div class="flex flex-wrap gap-6 mb-6">
<span class="bg-yellow-400 text-black px-3 py-1 rounded-full text-xs">
<?= $row['work_mode'] ?>
</span>

<span class="bg-red-900 px-3 py-1 rounded-full text-xs">
Deadline: <?= date("d M Y", strtotime($row['deadline'])) ?>
</span>

<span class="bg-gray-700 px-3 py-1 rounded-full text-xs">
    <i class="fa-solid fa-users"></i>

<?= $row['applicant'] ?> Applicants
</span>
</div>

<hr class="border-gray-700 mb-6">

<!-- DESCRIPTION -->
<h3 class="text-lg font-semibold mb-3">Job Description</h3>
<p class="text-gray-400 leading-relaxed mb-6">
<?= nl2br($row['description']) ?>
</p>

<!-- COMPANY -->
<button onclick="toggleCompany(this)"
class="bg-gray-800 border border-yellow-400 text-yellow-400 px-5 py-2 rounded-lg hover:bg-yellow-400 hover:text-black transition">
View Company Details
</button>

<div id="companySection" class="hidden mt-4 bg-[#111] p-4 rounded-lg">
<p class="mb-3"><b>Name:</b> <?= $row['cname'] ?></p>
<p class="mb-3"><b>Location:</b> <?= $row['company_location'] ?></p>

<?php if(!empty($row['website'])){ ?>
<p class="mb-3"><b>Website:</b> 
<a href="<?= $row['website'] ?>" target="_blank" class="text-yellow-400 underline">Visit</a>
</p>
<?php } ?>
<p class="mb-3"><b>Description:</b> 

<p class="text-gray-400 mt-2">
<?= nl2br($row['company_desc']) ?>
</p>
</div>

</div>

<!-- APPLY CARD -->
<div class="bg-[#1a1a1a] p-6 rounded-2xl border border-gray-800 sticky top-24">

<h3 class="text-lg font-semibold mb-4">Apply for this job</h3>
<?php if(isset($status) && $status == 'rejected'){ ?>
<p class="text-red-400 text-sm mb-2">
Your application was rejected. You cannot apply again for this job.
</p>
<?php } ?>
<?php if($pendingTest){ ?>

<a href="test.php?aid=<?=$aid?>" 
class="block w-full bg-yellow-400 text-black text-center py-3 rounded-lg font-semibold hover:bg-yellow-500">
Continue Test
</a>

<?php } elseif($canReapply){ ?>

<a href="apply_job.php?jid=<?=$jid?>" 
class="block w-full bg-green-500 text-black text-center py-3 rounded-lg font-semibold hover:bg-green-600">
Apply Again
</a>

<?php } elseif(!$applied){ ?>

<a href="apply_job.php?jid=<?=$jid?>" 
class="block w-full bg-yellow-400 text-black text-center py-3 rounded-lg font-semibold hover:bg-yellow-500">
Apply Now
</a>

<?php } else { ?>

<button class="w-full bg-gray-600 py-3 rounded-lg">
Application Submitted
</button>

<?php } ?>

</div>

</div>

<!-- ================= RIGHT ================= -->
<div class="lg:col-span-3">
    
    <div class="sticky top-24 
                h-[calc(100vh-120px)] 
                overflow-y-auto 
                pr-2 
                space-y-5 custom-scroll">

    <h3 class="text-lg font-semibold text-yellow-400 mb-2">
    More Jobs
    </h3>

    <?php while($job = mysqli_fetch_assoc($list_result)){ 
    $logo2 = !empty($job['logo']) 
    ? "../company/uploads/".$job['logo'] 
    : "https://via.placeholder.com/70";
    ?>

    <a href="?jid=<?=$job['jid']?>">

    <div class="bg-[#161616] p-4 rounded-xl mb-4 border hover:border-yellow-400 transition
    <?= ($job['jid']==$jid)?'border-yellow-400 bg-[#222]':'border-gray-800' ?>">

    <div class="flex items-center gap-3 mb-5 ">
    <img src="<?= $logo2 ?>" class="w-10 h-10 rounded bg-white p-1">

    <div>
        <h3 class="text-sm font-semibold"><?= $job['title'] ?></h3>
        <p class="text-gray-400 text-xs"><?= $job['cname'] ?></p>
        </div>
        </div>

        <p class="text-gray-400 text-xs">
        <?= substr($job['description'],0,50) ?>...
        </p>

        <p class="text-yellow-400 text-xs mt-2">
        ₹ <?= $job['salary'] ?> LPA
        </p>

    </div>
    </a>

    <?php } ?>

    </div>
    </div>

</div>

<script>
function toggleCompany(btn){
let sec = document.getElementById("companySection");

if(sec.classList.contains("hidden")){
sec.classList.remove("hidden");
btn.innerText="Hide Company Details";
}else{
sec.classList.add("hidden");
btn.innerText="View Company Details";
}
}
</script>

</body>
<?php include("../include/footer.php"); ?>

</html>