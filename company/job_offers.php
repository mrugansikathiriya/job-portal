<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";

// 🔐 Only company allowed
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

$company_uid = $_SESSION['uid'];

// ================= COMPANY INFO =================
$stmt = $conn->prepare("SELECT cid, cname FROM company WHERE uid=?");
$stmt->bind_param("i", $company_uid);
$stmt->execute();
$res = $stmt->get_result();
$company_data = $res->fetch_assoc();
$cid = $company_data['cid'];
$company_name = $company_data['cname'];

// ================= CSRF =================
$csrf_token = generateCSRFToken();

// ================= GET JOB OFFERS =================
$query = "
    SELECT jo.oid as offer_id, js.sid, js.sname, u.email as seeker_email, j.title as job_title, jo.message, jo.created_at, jo.status
    FROM job_offers jo
    JOIN job j ON jo.jid = j.jid
    JOIN job_seeker js ON jo.sid = js.sid
    JOIN users u ON js.uid = u.uid
    WHERE jo.cid = ?
    ORDER BY jo.created_at DESC
";

$stmt2 = $conn->prepare($query);
$stmt2->bind_param("i", $cid);
$stmt2->execute();
$res2 = $stmt2->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($company_name) ?> | Job Offers Sent</title>
    <link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>
<body class="bg-black text-white min-h-screen flex flex-col">

<?php include("../include/navbar.php"); ?>

<a href="cdashboard.php"
   class="inline-block mt-20 text-yellow-400 text-sm hover:underline">
   ← Back
</a>
<div class="flex-grow px-4 py-10">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl text-white mb-6">Candidates Offered by <?= htmlspecialchars($company_name) ?></h2>

        <div class="bg-[#0f0f0f] rounded-xl shadow-lg p-6 overflow-x-auto">
            <?php if($res2->num_rows > 0): ?>
                <table class="min-w-full divide-y divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-yellow-400">Candidate Name</th>
                            <th class="px-4 py-2 text-left text-yellow-400">Email</th>
                            <th class="px-4 py-2 text-left text-yellow-400">Job Title</th>
                            <th class="px-4 py-2 text-left text-yellow-400">Message</th>
                            <th class="px-4 py-2 text-left text-yellow-400">Status</th>
                            <th class="px-4 py-2 text-left text-yellow-400">Offered On</th>
                            <th class="px-4 py-2 text-left text-yellow-400">View Candidate Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php while($row = $res2->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['sname']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['seeker_email']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['job_title']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['message']) ?></td>
                            <td class="px-4 py-2">
                                <?php 
                                    switch($row['status']){
                                        case 'pending': echo "<span class='text-yellow-400'>Pending</span>"; break;
                                        case 'accepted': echo "<span class='text-green-400'>Accepted</span>"; break;
                                        case 'rejected': echo "<span class='text-red-400'>Rejected</span>"; break;
                                        default: echo "<span class='text-gray-400'>Unknown</span>";
                                    }
                                ?>
                            </td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['created_at']) ?></td>
                            <td class="px-4 py-2">
                                <a href="seeker_details.php?sid=<?= $row['sid'] ?>" class="text-blue-400 hover:underline" target="_blank">View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-400">No job offers sent yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mt-auto">
<?php include("../include/footer.php"); ?>
</div>
</body>
</html>