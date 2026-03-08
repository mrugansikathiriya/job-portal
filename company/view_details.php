<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];
$jid = intval($_GET['jid'] ?? 0);
$sql = "SELECT job.*, company.cname, company.logo, company.website,
        company.location AS company_location, company.description AS company_desc
        FROM job
        JOIN company ON job.cid = company.cid
        WHERE job.jid = '$jid' AND company.uid = '$uid'";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
    echo "Job not found or you do not have permission to view this job.";
    exit();
}

$jdata = mysqli_fetch_assoc($result);

// Logo fallback
$logo = !empty($jdata['logo']) ? "../company/uploads/".$jdata['logo'] : "https://via.placeholder.com/70";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Job | <?= htmlspecialchars($jdata['title']) ?></title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">

</head>

<body class="bg-black text-white min-h-screen px-4 py-6">

<?php include("../include/navbar.php"); ?>

<a href="cdashboard.php"
   class="inline-block mt-20 mb-4 text-yellow-400 text-sm hover:underline">
   ← Back
</a>


<div class="max-w-5xl mx-auto bg-[#0f0f0f] rounded-xl shadow-2xl 
p-6 sm:p-8 border border-white/10 text-white mb-20 justify-center">
    <!-- Job Header -->
    <div class="flex items-center gap-4 mb-6">
        <img src="<?= $logo ?>" class="w-20 h-20 rounded-lg bg-white p-1 object-cover">
        <div>
            <h1 class="text-3xl font-bold text-yellow-400"><?= htmlspecialchars($jdata['title']) ?></h1>
            <p class="text-gray-400"><?= htmlspecialchars($jdata['cname']) ?></p>
        </div>
    </div>

    <!-- Buttons -->
    <div class="flex gap-4 mb-8">
        <a href="edit_job.php?jid=<?= $jdata['jid'] ?>" 
           class="bg-yellow-400 text-black px-5 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
           Edit Job
        </a>
    <form method="POST" action="delete_job.php"
      onsubmit="return confirm('Are you sure you want to delete this job?');">

    <input type="hidden" name="jid" value="<?= $jdata['jid']; ?>">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

    <button type="submit"
        class="bg-red-500 text-white px-5 py-2 rounded-lg font-semibold hover:bg-red-600 transition">
        Delete Job
    </button>
</form>
    </div>

    <!-- Job Details -->
    <div class="space-y-3 text-gray-300 mb-6">
        <p><b>Location:</b> <?= htmlspecialchars($jdata['location']) ?></p>
        <p><b>Experience Required:</b> <?= htmlspecialchars($jdata['experience_required']) ?></p>
        <p><b>Job Type:</b> <?= htmlspecialchars($jdata['job_type']) ?></p>
        <p><b>Work Mode:</b> <?= htmlspecialchars($jdata['work_mode']) ?></p>
        <p><b>Salary:</b> ₹ <?= htmlspecialchars($jdata['salary']) ?></p>
        <p class="text-red-400"><b>Deadline:</b> <?= date("d M Y", strtotime($jdata['deadline'])) ?></p>
        <p><b>Total Applicants:</b> <?= htmlspecialchars($jdata['applicant']) ?></p>
    </div>

    <hr class="my-6 border-gray-700">

    <!-- Description -->
    <h2 class="text-xl font-semibold mb-3">Job Description</h2>
    <p class="text-gray-400 mb-6"><?= nl2br(htmlspecialchars($jdata['description'])) ?></p>

    <!-- Company Details -->
    <button onclick="toggleCompany()" 
        class="mb-6 bg-gray-800 border border-yellow-400 text-yellow-400 px-5 py-2 rounded-lg hover:bg-yellow-400 hover:text-black transition">
        View Company Details
    </button>

    <div id="companySection" class="hidden">
        <hr class="my-6 border-gray-700">
        <h3 class="text-xl font-semibold mb-4">Company Details</h3>
        <p class="text-gray-300 mb-2"><b>Company Name:</b> <?= htmlspecialchars($jdata['cname']) ?></p>
        <p class="text-gray-300 mb-2"><b>Location:</b> <?= htmlspecialchars($jdata['company_location']) ?></p>
        <?php if(!empty($jdata['website'])): ?>
            <p class="text-gray-300 mb-2"><b>Website:</b> 
                <a href="<?= $jdata['website'] ?>" target="_blank" class="text-yellow-400 underline">
                    <?= $jdata['website'] ?>
                </a>
            </p>
        <?php endif; ?>
        <div class="mt-4">
            <h4 class="text-lg font-semibold mb-2">About Company</h4>
            <p class="text-gray-400"><?= nl2br(htmlspecialchars($jdata['company_desc'])) ?></p>
        </div>
    </div>

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
<?php include("../include/footer.php"); ?>

</html>