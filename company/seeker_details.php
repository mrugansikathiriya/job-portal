<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

// only company login
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

// ✅ CSRF Token
$csrf_token = generateCSRFToken();

$company_uid = $_SESSION['uid'];

// ✅ FETCH COMPANY ID (Fix for Save Candidate)
$cid_query = mysqli_query($conn, "SELECT cid FROM company WHERE uid='$company_uid'");
$cid_row = mysqli_fetch_assoc($cid_query);
$cid = $cid_row['cid'] ?? 0;

// check sid
if(!isset($_GET['sid'])){
    echo "Invalid request";
    exit();
}

$sid = intval($_GET['sid']); // sanitize input

// 🔹 FILTER
$filter = isset($_GET['filter']) ? $_GET['filter'] : "all";

// 🔹 FETCH SEEKER
$seeker_sql = "SELECT * FROM job_seeker WHERE sid='$sid'";
$seeker_result = mysqli_query($conn, $seeker_sql);

if(!$seeker_result || mysqli_num_rows($seeker_result) == 0){
    echo "Seeker not found";
    exit();
}

$seeker = mysqli_fetch_assoc($seeker_result);

// 🔹 JOB QUERY
if($filter == "my"){
    $job_sql = "SELECT job.title, job.location, job.salary, 
                       application.applied_at, application.status,
                       company.cname
                FROM application
                JOIN job ON application.jid = job.jid
                JOIN company ON job.cid = company.cid
                WHERE application.sid='$sid'
                  AND company.uid='$company_uid'
                ORDER BY application.applied_at DESC";
} else {
    $job_sql = "SELECT job.title, job.location, job.salary, 
                       application.applied_at, application.status,
                       company.cname
                FROM application
                JOIN job ON application.jid = job.jid
                JOIN company ON job.cid = company.cid
                WHERE application.sid='$sid'
                ORDER BY application.applied_at DESC";
}

// 🔹 CHECK IF CANDIDATE IS SAVED
$check_saved = mysqli_query($conn, 
    "SELECT 1 FROM saved_candidate WHERE cid='$cid' AND sid='$sid'"
);
$is_saved = mysqli_num_rows($check_saved) > 0;

$jobs = mysqli_query($conn, $job_sql);

// image
$img = !empty($seeker['profile_image']) 
        ? "../seeker/uploads/".$seeker['profile_image'] 
        : "https://via.placeholder.com/120";
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Career craft | Seeker Details</title>
        <link href="../dist/styles.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="icon" href="../image/logo3.jpg" type="image/png">
    </head>

    <body class="bg-black text-white p-8">

        <?php include("../include/navbar.php"); ?>

        <a href="find_talent.php"
        class="inline-block mt-20 text-yellow-400 text-sm hover:underline">
        ← Back
        </a>

        <div class="max-w-5xl mx-auto mb-10">
        <h2 class="text-3xl md:text-4xl font-semibold text-[#D7AE27] mb-16 text-center">
                Job_Seeker
                    <span class="relative inline-block text-white">
                        Profile
                        <span class="absolute left-0 top-full mt-5 w-full h-1 bg-[#D7AE27] rounded-sm"></span>
                    </span>
                </h2>

            <!-- PROFILE CARD -->
        <div class="bg-[#1a1a1a] p-8 rounded-2xl border border-gray-700 shadow-lg relative">

            <!-- SAVE BUTTON -->
            <form method="POST" action="toggle_save_candidate.php" class="absolute top-4 right-4">
                <input type="hidden" name="sid" value="<?= $sid ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <button type="submit" class="text-xl">
                    <i class="<?= $is_saved 
                        ? 'fa-solid fa-bookmark text-yellow-400'
                        : 'fa-regular fa-bookmark text-white hover:text-yellow-400' ?>">
                    </i>
                </button>
            </form>        

            <div class="flex items-center gap-6">
                <img src="<?= htmlspecialchars($img) ?>" 
                    class="w-28 h-28 rounded-xl object-cover bg-white p-1">

                <div>
                    <h2 class="text-3xl font-bold"><?= htmlspecialchars($seeker['sname']) ?></h2>
                    <p class="text-gray-400"><?= htmlspecialchars($seeker['education']) ?></p>

                    <div class="flex gap-2 mt-2 text-sm">
                        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
                            <?= htmlspecialchars($seeker['experience']) ?>
                        </span>
                        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
                            <?= htmlspecialchars($seeker['skillname']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-xl text-yellow-400 font-semibold mb-2">About</h3>
                <p class="text-gray-300"><?= nl2br(htmlspecialchars($seeker['bio'])) ?></p>
            </div>

            <!-- CONTACT BUTTON -->
            <a href="contact_seeker.php?sid=<?= $sid ?>"
            class="inline-block mt-6 bg-yellow-400 text-black px-6 py-2 rounded-lg hover:bg-yellow-500 font-semibold">
            Contact Seeker
            </a>
                
        </div>

        <!-- FILTER BUTTONS -->
        <div class="flex gap-4 mt-8">
            <a href="?sid=<?= $sid ?>&filter=all"
            class="px-5 py-2 rounded-lg font-semibold <?= $filter=='all'?'bg-yellow-400 text-black':'bg-gray-700 text-white' ?>">
            All Applications
            </a>

            <a href="?sid=<?= $sid ?>&filter=my"
            class="px-5 py-2 rounded-lg font-semibold <?= $filter=='my'?'bg-yellow-400 text-black':'bg-gray-700 text-white' ?>">
            My Company Applications
            </a>
        </div>

        <!-- JOB LIST -->
        <div class="mt-6">
        <h3 class="text-2xl font-bold text-yellow-400 mb-6">Job Applications</h3>

        <?php if(mysqli_num_rows($jobs) > 0): ?>
        <div class="grid md:grid-cols-2 gap-6">
        <?php while($row = mysqli_fetch_assoc($jobs)) { 
            $status_color = "bg-yellow-500";
            if($row['status']=="selected") $status_color="bg-green-500";
            if($row['status']=="rejected") $status_color="bg-red-500";
            if($row['status']=="shortlisted") $status_color="bg-blue-500";
        ?>
            <div class="bg-[#1a1a1a] p-6 md:p-8 rounded-2xl border border-gray-700 shadow-md hover:shadow-yellow-500/30 transition transform hover:-translate-y-1">
            <h4 class="text-lg font-semibold"><?= htmlspecialchars($row['title']) ?></h4>
            <p class="text-gray-400 text-sm mt-1">🏢 <?= htmlspecialchars($row['cname']) ?></p>
            <p class="text-gray-400 text-sm mt-1">📍 <?= htmlspecialchars($row['location']) ?></p>
            <p class="text-gray-300 mt-2">💰 ₹ <?= htmlspecialchars($row['salary']) ?> LPA</p>
            <p class="text-gray-400 text-sm mt-2">Applied on: <?= date("d M Y", strtotime($row['applied_at'])) ?></p>
            <span class="<?= $status_color ?> text-black px-3 py-1 rounded-full text-xs mt-3 inline-block">
                <?= ucfirst($row['status']) ?>
            </span>
        </div>
        <?php } ?>
        </div>
        <?php else: ?>
        <p class="text-gray-400 mt-4">No applications found.</p>
        <?php endif; ?>

        </div>

        </div>
        <?php include("../include/footer.php"); ?>
    </body>
</html>