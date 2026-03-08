<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

// Fetch the company's ID
$companyRes = mysqli_query($conn, "SELECT cid FROM company WHERE uid='$uid'");
$companyData = mysqli_fetch_assoc($companyRes);
$cid = $companyData['cid'] ?? 0;

if(!$cid){
    echo "Company not found!";
    exit();
}

// Fetch all jobs posted by this company
$sql = "SELECT * FROM job WHERE cid='$cid' ORDER BY posted_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Craft | Jobs Posted</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png"></head>

</head>

<body class="bg-[#0f0f0f] text-white min-h-screen">
<?php include("../include/navbar.php"); ?>
<a href="cdashboard.php"
   class="inline-block mt-20 text-yellow-400 text-sm hover:underline">
   ← Back
</a>
<?php if(isset($_SESSION['jobedit_success'])): ?>
<div id="flashMessage"
     class="fixed top-20 right-5 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 
            flex items-center justify-between gap-4 min-w-[280px] 
            transition-opacity duration-500">

    <span><?= $_SESSION['jobedit_success']; ?></span>

    <button onclick="closeFlash()"
            class="text-white text-xl font-bold hover:text-gray-200 leading-none">
        &times;
    </button>
</div>
<?php unset($_SESSION['jobedit_success']); ?>
<?php endif; ?>
<div class="max-w-7xl mx-auto px-6 mt-20 mb-10">

<h2 class="text-3xl text-center font-semibold mb-12">My Posted Jobs</h2>

<?php if(mysqli_num_rows($result) == 0): ?>
    <p class="text-center text-gray-400">No jobs posted yet.</p>
<?php else: ?>
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

<?php while($row = mysqli_fetch_assoc($result)): ?>

<div class="bg-[#161616] p-6 rounded-2xl border border-gray-800 hover:border-yellow-400 transition-all duration-300 relative">

    <!-- Job Info -->
    <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($row['title']); ?></h3>
    <p class="text-gray-400 mb-2">
        <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($row['location']); ?>
    </p>
    <p class="text-gray-400 mb-2">
        <i class="fa-solid fa-briefcase"></i> <?= htmlspecialchars($row['experience_required']); ?>
    </p>
    <p class="text-gray-400 mb-2">
        <i class="fa-solid fa-clock"></i> <?= htmlspecialchars($row['job_type']); ?>
    </p>

    <!-- Buttons -->
    <div class="flex gap-2 mt-4">

        <!-- View Button -->
        <a href="view_details.php?jid=<?= $row['jid']; ?>" 
           class="flex-1 text-center bg-yellow-400 text-black px-3 py-2 rounded-lg hover:bg-yellow-500 transition">
           View
        </a>

        <!-- Edit Button -->
        <a href="edit_job.php?id=<?= $row['jid']; ?>" 
           class="flex-1 text-center bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition">
           Edit
        </a>

      <form method="POST" action="delete_job.php" class="flex-1"
      onsubmit="return confirm('Are you sure you want to delete this job?');">
      
    <input type="hidden" name="jid" value="<?= $row['jid']; ?>">
<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

    <button type="submit"
        class="w-full bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition">
        Delete
    </button>
</form>
    </div>
</div>

<?php endwhile; ?>

</div>
<?php endif; ?>

</div>
<?php include("../include/footer.php"); ?>
<script>
function closeFlash() {
    const flash = document.getElementById("flashMessage");
    if (flash) {
        flash.style.opacity = "0";
        setTimeout(() => flash.remove(), 500);
    }
}

// Auto hide after 1 minute (60000 milliseconds)
setTimeout(function(){
    closeFlash();
}, 60000);
</script>
</body>
</html>