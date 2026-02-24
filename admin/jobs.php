<?php
session_start();
require "../config/db.php";

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
        <?php if($row['status'] == 'active') { ?>
            <span class="bg-green-500 px-3 py-1 rounded text-sm">Active</span>
        <?php } else { ?>
            <span class="bg-red-500 px-3 py-1 rounded text-sm">Closed</span>
        <?php } ?>
    </td>

    <td class="p-3"><?= $row['posted_at']; ?></td>

   
  <td class="p-3">

<?php if($row['is_approve'] == 'pending') { ?>

    <div class="flex flex-col items-center gap-2">

        <a href="approve_job.php?jid=<?= $row['jid']; ?>"
           class="w-24 text-center bg-yellow-500 px-3 py-1 rounded text-black text-sm hover:bg-yellow-600 transition">
           Approve
        </a>

        <a href="reject_job.php?jid=<?= $row['jid']; ?>"
           class="w-24 text-center bg-red-500 px-3 py-1 rounded text-white text-sm hover:bg-red-600 transition">
           Reject
        </a>

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
    <a href="delete_job.php?jid=<?= $row['jid']; ?>"
       onclick="return confirm('Are you sure?')"
       class="bg-red-500 px-3 py-1 rounded hover:bg-red-600 text-sm">
       Delete
    </a>
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

</body>
</html>