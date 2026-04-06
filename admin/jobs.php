<?php
require "../config/db.php";
require "admin_auth.php";
require "../authc/csrf.php";
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}
$status = $_GET['status'] ?? '';

$query = "
    SELECT job.*, company.cname 
    FROM job
    INNER JOIN company ON job.cid = company.cid
";

if ($status == 'approved') {
    $query .= " WHERE job.is_approve = 'approved'";
} 
elseif ($status == 'pending') {
    $query .= " WHERE job.is_approve = 'pending'";
}
elseif ($status == 'rejected') {
    $query .= " WHERE job.is_approve = 'rejected'";
}

$query .= " ORDER BY job.jid DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Craft | Manage Jobs</title>

    <link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black/90 text-white min-h-screen p-8">

<div class="flex items-center gap-3 mb-6">
    <img src="../image/logo3.jpg" class="h-10 w-10 object-contain">
    <span class="text-xl font-bold text-[#D7AE27]">
        CareerCraft
    </span>
</div>

<div class="max-w-7xl mx-auto">
<div class="flex justify-center gap-4 mb-6">

    <a href="jobs.php"
    class="px-4 py-2 rounded <?= $status=='' ? 'bg-[#D7AE27] text-black' : 'bg-gray-700' ?>">
    All
    </a>

    <a href="jobs.php?status=pending"
    class="px-4 py-2 rounded <?= $status=='pending' ? 'bg-yellow-500 text-black' : 'bg-gray-700' ?>">
    Pending
    </a>

    <a href="jobs.php?status=approved"
    class="px-4 py-2 rounded <?= $status=='approved' ? 'bg-green-600 text-white' : 'bg-gray-700' ?>">
    Approved
    </a>

    <a href="jobs.php?status=rejected"
    class="px-4 py-2 rounded <?= $status=='rejected' ? 'bg-red-600 text-white' : 'bg-gray-700' ?>">
    Rejected
    </a>

</div>
<h1 class="text-3xl font-bold mb-8 text-[#D7AE27] text-center">
    Manage Jobs
</h1>

<div class="overflow-x-auto bg-black/70 border border-[#D7AE27]/30 rounded-xl shadow-lg">

<table class="w-full text-left text-sm">

<thead class="bg-[#D7AE27] text-black">
<tr>
    <th class="p-3">Job ID</th>
    <th class="p-3">Company</th>
    <th class="p-3">Title</th>
    <th class="p-3">Location</th>
    <th class="p-3">Salary</th>
    <th class="p-3">Experience</th>
    <th class="p-3">Skills</th>
    <th class="p-3">Job Type</th>
    <th class="p-3">Work Mode</th>
    <th class="p-3">Deadline</th>
    <th class="p-3">Vacancy</th>
    <th class="p-3">Applicants</th>
    <th class="p-3">Status</th>
    <th class="p-3">Posted At</th>
    <th class="p-3">Description</th>

    <th class="p-3">is_approve</th>
    <th class="p-3 text-center">Actions</th>

</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr class="border-b border-gray-700 hover:bg-black/50 transition">

    <td class="p-3"><?= $row['jid']; ?></td>
    <td class="p-3"><?= $row['cname']; ?></td>
    <td class="p-3"><?= $row['title']; ?></td>
    <td class="p-3"><?= $row['location']; ?></td>

    <td class="p-3">
        <?= $row['salary']; ?>
        <?= $row['salary_type'] ? "(".$row['salary_type'].")" : ""; ?>
    </td>

    <td class="p-3"><?= $row['experience_required']; ?></td>

    <td class="p-3"><?= $row['skillname']; ?></td>

    <td class="p-3"><?= ucfirst($row['job_type']); ?></td>

    <td class="p-3"><?= ucfirst($row['work_mode']); ?></td>

    <td class="p-3"><?= $row['deadline']; ?></td>

    <td class="p-3"><?= $row['vacancy']; ?></td>

    <td class="p-3"><?= $row['applicant']; ?></td>

    <td class="p-3">
        <?php if($row['status'] == 'open') { ?>
            <span class="bg-green-500 px-3 py-1 rounded text-sm">Active</span>
        <?php } else { ?>
            <span class="bg-red-500 px-3 py-1 rounded text-sm">Closed</span>
        <?php } ?>
    </td>

    <td class="p-3"><?= $row['posted_at']; ?></td>

<td class="p-3">
<?php if(!empty($row['description'])) { ?>
    <button
        class="text-blue-400 hover:underline view-job-desc-btn"
        data-desc="<?php echo htmlspecialchars($row['description']); ?>">
        View Description
    </button>
<?php } else { ?>
    <span class="text-gray-400">No Description</span>
<?php } ?>
</td>
<td class="p-3">

<?php if($row['is_approve'] == 'pending') { ?>

    <div class="flex flex-col items-center gap-2">

        <!-- Approve Button -->
        <form method="POST" action="approve_job.php" class="w-full">
            <input type="hidden" name="jid" value="<?= $row['jid']; ?>">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition">
                Approve
            </button>
        </form>

        <!-- Reject Button -->
        <form method="POST" action="reject_job.php" class="w-full">
            <input type="hidden" name="jid" value="<?= $row['jid']; ?>">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition">
                Reject
            </button>
        </form>

    </div>

<?php } elseif($row['is_approve'] == 'approved') { ?>

    <span class="bg-green-600 px-3 py-1 rounded text-sm block text-center">
        Approved
    </span>

<?php } elseif($row['is_approve'] == 'rejected') { ?>

    <span class="bg-red-600 px-3 py-1 rounded text-sm block text-center">
        Rejected
    </span>

<?php } ?>

</td>

<td class="p-3 text-center">
 <form method="POST" action="delete_job.php"
      onsubmit="return confirm('Are you sure you want to delete this job?');">

    <input type="hidden" name="jid" value="<?= $row['jid']; ?>">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

    <button type="submit"
        class="bg-gray-800 hover:bg-red-700 text-red-500 hover:text-white px-3 py-1 rounded text-sm transition">
        Delete
    </button>
</form>
</td>
</tr>

<?php } ?>

</tbody>
</table>
</div>

<div class="mt-8">
    <a href="admin_dashboard.php"
       class="bg-[#D7AE27] text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition">
       Back to Dashboard
    </a>
</div>

</div>
<!-- JOB DESCRIPTION MODAL -->
<div id="jobDescModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">
    <div class="bg-[#0f0f0f] text-white p-6 rounded-xl max-w-2xl w-full shadow-xl border border-white/20">
       
        <h2 class="text-xl font-bold mb-4 text-[#D7AE27]">
            Job Description
        </h2>
       
        <p id="jobDescContent"
           class="text-gray-300 max-h-80 overflow-y-auto whitespace-pre-line"></p>
       
        <div class="mt-6 text-right">
            <button id="closeJobDescModal"
                class="bg-[#D7AE27] text-black px-5 py-2 rounded hover:bg-yellow-500">
                Close
            </button>
        </div>
    </div>
</div>


<script>
const jobModal = document.getElementById("jobDescModal");
const jobContent = document.getElementById("jobDescContent");
const closeJobModal = document.getElementById("closeJobDescModal");

// Open modal
document.querySelectorAll(".view-job-desc-btn").forEach(btn => {
    btn.addEventListener("click", function(){
        const desc = this.getAttribute("data-desc");
        jobContent.textContent = desc;
        jobModal.classList.remove("hidden");
        jobModal.classList.add("flex");
    });
});

// Close button
closeJobModal.addEventListener("click", () => {
    jobModal.classList.add("hidden");
    jobModal.classList.remove("flex");
});

// Close on outside click
jobModal.addEventListener("click", (e) => {
    if(e.target === jobModal){
        jobModal.classList.add("hidden");
        jobModal.classList.remove("flex");
    }
});
</script> 
</body>
</html>