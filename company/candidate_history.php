<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

date_default_timezone_set('Asia/Kolkata');

/* Check company login */
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

// get company id
$cid_query = mysqli_query($conn, "SELECT cid FROM company WHERE uid='$uid'");
$cid_row = mysqli_fetch_assoc($cid_query);
$cid = $cid_row['cid'] ?? 0;

// ✅ CSRF TOKEN
$csrf_token = generateCSRFToken();
// ✅ FILTER (safe)
$filter = $_GET['filter'] ?? 'all';
$filter = in_array($filter, ['all','saved']) ? $filter : 'all';

/* ================= QUERY ================= */

if($filter == 'saved'){

    $sql = "SELECT js.*, u.created_at
            FROM saved_candidate sc
            JOIN job_seeker js ON sc.sid = js.sid
            JOIN users u ON js.uid = u.uid
            WHERE sc.cid='$cid'
            ORDER BY sc.scid DESC";

} else {

    $sql = "SELECT js.*, u.created_at
            FROM job_seeker js
            JOIN users u ON js.uid = u.uid
            ORDER BY u.created_at DESC";

}

$result = mysqli_query($conn, $sql);
$num_candidates = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Candidate History</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white min-h-screen">

<?php include("../include/navbar.php"); ?>

<a href="cdashboard.php"
class="inline-block mt-20 text-yellow-400 text-sm hover:underline ml-10">
← Back
</a>

<div class="max-w-7xl mx-auto px-6 mt-5 mb-10">
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
</form>
<!-- Heading -->
<h2 class="text-3xl text-center font-semibold mb-8">
<?= ($filter == 'saved') ? "Saved Candidates" : "All Candidates"; ?>
</h2>

<!-- FILTER BUTTONS -->
<div class="flex justify-center gap-4 mb-10 flex-wrap">

<a href="?filter=all"
class="px-5 py-2 rounded-full border <?= ($filter=='all') ? 'bg-yellow-400 text-black' : 'border-gray-600'; ?>">
All Candidates
</a>

<a href="?filter=saved"
class="px-5 py-2 rounded-full border <?= ($filter=='saved') ? 'bg-yellow-400 text-black' : 'border-gray-600'; ?>">
Saved Candidates
</a>

</div>

<!-- GRID -->
<?php if($num_candidates > 0): ?>
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">

<?php while($row = mysqli_fetch_assoc($result)) { 

    // IMAGE
    $img = !empty($row['profile_image']) 
        ? "../seeker/uploads/".$row['profile_image'] 
        : "https://via.placeholder.com/70";

    $date = date("d M Y", strtotime($row['created_at']));

?>

<!-- CARD -->
<div class="bg-[#161616] p-6 rounded-2xl border border-gray-800 hover:border-yellow-400 transition-all duration-300 relative">

    <!-- TOP -->
    <div class="flex items-center gap-4">

        <img src="<?= $img ?>" 
            class="w-16 h-16 rounded-xl object-cover bg-white p-1">

        <div>
            <h3 class="text-lg font-semibold"><?= $row['sname'] ?></h3>
            <p class="text-gray-400 text-sm"><?= $row['education'] ?></p>
            <p class="text-gray-500 text-xs mt-1">
                <?= isset($date) ? "Joined: ".$date : "" ?>
            </p>
        </div>

    </div>

    <!-- TAGS -->
    <div class="flex flex-wrap gap-2 mt-4 text-xs">

        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
            <?= $row['experience'] ?>
        </span>

        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
            <?= $row['skillname'] ?>
        </span>

    </div>

    <!-- BIO -->
    <p class="text-gray-400 text-sm mt-4 leading-relaxed">
        <?= !empty($row['bio']) ? substr($row['bio'], 0, 80)."..." : "No description available" ?>
    </p>

    <!-- STATUS -->
    <div class="flex justify-between items-center mt-5">

        <span class="text-green-400 text-sm font-medium">
            ● Available for Hiring
        </span>

        <span class="text-gray-500 text-xs">
            Active
        </span>

    </div>

    <!-- BUTTON -->
    <a href="seeker_details.php?sid=<?= $row['sid'] ?>" 
    class="block mt-5 bg-[#D7AE27] text-black text-center py-2 rounded-xl 
            font-semibold hover:bg-yellow-500 transition">
        View Profile
    </a>

</div>

<?php } ?>
</div>
<?php else: ?>
<p class="text-gray-400 text-center mt-10 text-lg">
<?= ($filter == 'saved') ? "No saved candidates found." : "No candidates found." ?>
</p>
<?php endif; ?>

</div>

<?php include("../include/footer.php"); ?>

</body>
</html>