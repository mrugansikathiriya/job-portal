<?php
session_start();
require "../config/db.php";

require "../authc/csrf.php";
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

/* Get company id */
$csql = "SELECT cid,cname FROM company WHERE uid='$uid' LIMIT 1";
$cres = mysqli_query($conn,$csql);

if(mysqli_num_rows($cres)==0){
    die("Company not registered.");
}

$company = mysqli_fetch_assoc($cres);
$cid = $company['cid'];

/* Get jobs of company */
$sql = "SELECT * FROM job WHERE cid='$cid' ORDER BY posted_at DESC";
$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | View Applicants</title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white min-h-screen">
    <?php include("../include/navbar.php"); ?>

<a href="cdashboard.php"
   class="inline-block mt-20 text-yellow-400 text-sm hover:underline">
   ← Back
</a>
<div class="max-w-6xl mx-auto py-12 px-6">

<h2 class="text-3xl font-semibold text-center mb-10 text-[#D7AE27]">
View Applicants
</h2>

<div class="bg-[#1a1a1a] rounded-xl border border-gray-800 p-6">

<table class="w-full text-left border-collapse">

<thead>
<tr class="border-b border-gray-800 hover:bg-[#222] transition">
    <th class="py-3">Job Title</th>
<th>Total Applicants</th>
<th>Deadline</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php while($job=mysqli_fetch_assoc($result)){ ?>

<tr class="border-b border-gray-800 hover:bg-[#222] transition">

<td class="py-3">
<?php echo $job['title']; ?>
</td>

<td>
<?php echo $job['applicant']; ?>
</td>

<td>
<?php echo $job['deadline']; ?>
</td>

<td>
<a href="view_applicant.php?jid=<?php echo $job['jid']; ?>"
class="text-[#D7AE27] underline text-sm">
View Applicants
</a>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>
</div>

</body>
<?php include("../include/footer.php"); ?>

</html>