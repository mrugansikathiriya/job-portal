<?php
session_start();
require "../config/db.php";

// 🔐 ADMIN CHECK
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}

// 🚫 BLOCK COMPANY
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['cname'])){
    $cname = $_POST['cname'];

    // update user status
    $stmt = $conn->prepare("UPDATE users SET status='blocked' WHERE company_name=?");
    $stmt->bind_param("s", $cname);
    $stmt->execute();
}

// 📊 FETCH FRAUD REPORTS
$query = "
    SELECT fr.cname,u.uid,
           COUNT(fr.fr_id) as total,
           MAX(fr.details) as details,
           u.status
    FROM fraud_reports fr
    LEFT JOIN users u ON fr.cname = u.uname
    GROUP BY fr.cname
    ORDER BY total DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Fraud Reports</title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white">

<div class="max-w-6xl mx-auto p-6">

<h1 class="text-3xl font-bold text-[#D7AE27] mb-6">⚠ Fraud Reports</h1>

<div class="overflow-x-auto">

<table class="w-full border border-gray-700 text-left">

<thead class="bg-[#D7AE27] text-black">
<tr>
<th class="p-3">Company Name</th>
<th class="p-3">User ID</th>

<th class="p-3">Reports</th>
<th class="p-3">Latest Detail</th>
<th class="p-3">Status</th>
<th class="p-3">Action</th>
</tr>
</thead>

<tbody>

<?php if($result && $result->num_rows > 0){ ?>
<?php while($row = $result->fetch_assoc()) { ?>

<tr class="border-b border-gray-700 hover:bg-white/5">

<td class="p-3"><?php echo htmlspecialchars($row['cname']); ?></td>
<td class="p-3"><?php echo $row['uid']; ?></td>
<td class="p-3 text-yellow-400 font-bold">
<?php echo $row['total']; ?>
</td>

<td class="p-3"><?php echo htmlspecialchars($row['details']); ?></td>

<td class="p-3">
<?php if($row['status'] == 'blocked'){ ?>
    <span class="text-red-500 font-semibold">Blocked</span>
<?php } else { ?>
    <span class="text-green-400">Active</span>
<?php } ?>
</td>

<td class="p-3">

<?php if($row['status'] != 'blocked'){ ?>

<form method="POST">
<input type="hidden" name="cname" value="<?php echo $row['cname']; ?>">

<p
class="bg-red-600 px-1 py-1 rounded hover:bg-red-700">
Block
</p>
</form>

<?php } else { ?>

<p class="bg-gray-600 px-1 py-1 rounded cursor-not-allowed">
Blocked
</p>

<?php } ?>

</td>

</tr>

<?php } ?>
<?php } else { ?>

<tr>
<td colspan="5" class="p-4 text-center text-gray-400">
No Fraud Reports Found
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