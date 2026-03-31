<?php
session_start();
require "../config/db.php";

// fetch fraud reports with company status
$query = "
SELECT fr.company_email,
       COUNT(fr.fr_id) as total,
       MAX(fr.details) as details,
       u.status
FROM fraud_reports fr
LEFT JOIN users u ON fr.company_email = u.email
GROUP BY fr.company_email
ORDER BY total DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Fraud Reports</title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body class="bg-black text-white">

<div class="max-w-6xl mx-auto p-6">

<h1 class="text-3xl font-bold text-[#D7AE27] mb-6">⚠ Fraud Reports</h1>

<div class="overflow-x-auto">

<table class="w-full border border-gray-700 text-left">

<thead class="bg-[#D7AE27] text-black">
<tr>
<th class="p-3">Company Email</th>
<th class="p-3">Reports</th>
<th class="p-3">Latest Detail</th>
<th class="p-3">Status</th>
<th class="p-3">Action</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()) { ?>

<tr class="border-b border-gray-700 hover:bg-white/5">

<td class="p-3"><?php echo $row['company_email']; ?></td>

<td class="p-3 text-yellow-400 font-bold">
<?php echo $row['total']; ?>
</td>

<td class="p-3"><?php echo $row['details']; ?></td>

<td class="p-3">
<?php if($row['status'] == 'blocked'){ ?>
    <span class="text-red-500 font-semibold">Blocked</span>
<?php } else { ?>
    <span class="text-green-400">Active</span>
<?php } ?>
</td>

<td class="p-3">

<?php if($row['status'] != 'blocked'){ ?>

<form method="POST" action="block_company.php">
<input type="hidden" name="email" value="<?php echo $row['company_email']; ?>">

<button type="submit"
class="bg-red-600 px-3 py-1 rounded hover:bg-red-700">
Block
</button>
</form>

<?php } else { ?>

<button class="bg-gray-600 px-3 py-1 rounded cursor-not-allowed">
Blocked
</button>

<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>
</html>