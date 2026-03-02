<?php
session_start();
include("../config/db.php");;

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'seeker'){
    header("Location: ../auth/login.php");
    exit();
}


$sql = "SELECT job.*, 
        company.cname, 
        company.logo, 
        company.website, 
        company.location AS company_location,
        company.description AS company_desc
        FROM job
        JOIN company ON job.cid = company.cid";

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
    $check = mysqli_query($conn,"SELECT 1 FROM saved_job WHERE uid='$uid'");
    if(mysqli_num_rows($check) > 0){
        $saved = true;
    }
}

// Logo fallback
$logo = !empty($row['logo']) 
        ? "../company/uploads/".$row['logo'] 
        : "https://via.placeholder.com/70";
?>
<!DOCTYPE html>
<html>
<head>
<title>Career craft | Find Job</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">

</head>

<body class="bg-[#0f0f0f] text-white min-h-screen"> 

<div class="max-w-4xl mx-auto mt-12 bg-[#1a1a1a] p-8 rounded-2xl border border-gray-800">

<!-- Top Section -->
<div class="flex justify-between items-start">

<div class="flex items-center gap-4">
<img src="<?php echo $logo; ?>"
class="w-16 h-16 rounded-lg bg-white p-1">

<div>
<h2 class="text-2xl font-semibold"><?php echo $row['title']; ?></h2>
<p class="text-gray-400"><?php echo $row['cname']; ?></p>
</div>
</div>

<?php if(isset($_SESSION['uid'])) { ?>
<a href="<?php echo $saved ? 'unsave_job.php' : 'save_job.php'; ?>?jid=<?php echo $row['jid']; ?>"
class="text-2xl <?php echo $saved ? 'text-yellow-400' : 'text-gray-400 hover:text-yellow-400'; ?>">
<i class="<?php echo $saved ? 'fa-solid' : 'fa-regular'; ?> fa-bookmark"></i>
</a>
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
<button onclick="toggleCompany()"
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
    <a href="apply_job.php?jid=<?= $row['jid']; ?>"
       class="inline-block bg-yellow-400 text-black px-6 py-2 rounded-lg font-medium hover:bg-yellow-500 mt-6">
        Apply Now
    </a>
<?php endif; ?>

</div>

<script>
function toggleCompany() {
    var section = document.getElementById("companySection");
    var btn = event.target;

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
</html>