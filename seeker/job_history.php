<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

date_default_timezone_set('Asia/Kolkata');

/* Check login */
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'seeker'){
    header("Location: ../auth/login.php");
    exit();
}
$uid = $_SESSION['uid'] ?? 0;
// 🔥 Convert uid → sid
$seekerRes = mysqli_query($conn, "SELECT sid FROM job_seeker WHERE uid='$uid'");
$seekerData = mysqli_fetch_assoc($seekerRes);
$sid = $seekerData['sid'] ?? 0;
// ✅ FILTER
$filter = $_GET['filter'] ?? 'all';

// ✅ QUERY
if($filter == 'saved'){

$sql = "SELECT job.*, company.cname, company.logo,
        1 AS saved,
        TIMESTAMPDIFF(SECOND, job.posted_at, NOW()) as seconds_old
        FROM saved_job
        JOIN job ON saved_job.jid = job.jid
        JOIN company ON job.cid = company.cid
        WHERE saved_job.uid='$uid'
        AND job.is_approve='approved'";

}
elseif($filter == 'applied'){
$sql = "SELECT job.*, company.cname, company.logo,
        1 AS applied,
        TIMESTAMPDIFF(SECOND, job.posted_at, NOW()) as seconds_old
        FROM application
        JOIN job ON application.jid = job.jid
        JOIN company ON job.cid = company.cid
        WHERE application.uid='$uid'
        AND job.is_approve='approved'
        ORDER BY application.aid DESC";

}

/* ✅ NEW: INTERVIEW FILTER */
elseif($filter == 'interview'){

$sql = "SELECT job.*, company.cname, company.logo,
        1 AS applied,
        TIMESTAMPDIFF(SECOND, job.posted_at, NOW()) as seconds_old
        FROM application
        JOIN job ON application.jid = job.jid
        JOIN company ON job.cid = company.cid
        WHERE application.uid='$uid'
        AND application.status='interview_scheduled'
        AND job.is_approve='approved'
        ORDER BY application.aid DESC";

}

/* ✅ NEW: OFFERED FILTER */
elseif($filter == 'offered'){
$sql = "SELECT job.*, company.cname, company.logo,
        job_offers.status AS offer_status,
        TIMESTAMPDIFF(SECOND, job.posted_at, NOW()) as seconds_old
        FROM job_offers
        INNER JOIN job ON job_offers.jid = job.jid
        INNER JOIN company ON job.cid = company.cid
        WHERE job_offers.sid='$sid'
        ORDER BY job_offers.oid DESC";
}

else{
$sql = "SELECT job.*, company.cname, company.logo,
        EXISTS(
            SELECT 1 FROM saved_job 
            WHERE saved_job.jid = job.jid 
            AND saved_job.uid = '$uid'
        ) AS saved,
        TIMESTAMPDIFF(SECOND, job.posted_at, NOW()) as seconds_old
        FROM job
        JOIN company ON job.cid = company.cid
        WHERE job.is_approve='approved'
        ORDER BY job.posted_at DESC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Job History</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white min-h-screen">

<?php include("../include/navbar.php"); ?>

<a href="sdashboard.php"
class="inline-block mt-20 text-yellow-400 text-sm hover:underline ml-10">
← Back
</a>

<div class="max-w-7xl mx-auto px-6 mt-5 mb-10">

<!-- Heading -->
<h2 class="text-3xl text-center font-semibold mb-8">
<?php
if($filter == 'saved'){
    echo "Saved Jobs";
}
elseif($filter == 'applied'){
    echo "Applied Jobs";
}
elseif($filter == 'interview'){
    echo "Interview Jobs";
}
elseif($filter == 'offered'){
    echo "Offered Jobs";
}
else{
    echo "All Jobs";
}
?>
</h2>

<!-- FILTER BUTTONS -->
<div class="flex justify-center gap-4 mb-10 flex-wrap">

<a href="?filter=all"
class="px-5 py-2 rounded-full border <?php echo ($filter=='all') ? 'bg-yellow-400 text-black' : 'border-gray-600'; ?>">
All Jobs
</a>

<a href="?filter=applied"
class="px-5 py-2 rounded-full border <?php echo ($filter=='applied') ? 'bg-yellow-400 text-black' : 'border-gray-600'; ?>">
Applied Jobs
</a>

<a href="?filter=saved"
class="px-5 py-2 rounded-full border <?php echo ($filter=='saved') ? 'bg-yellow-400 text-black' : 'border-gray-600'; ?>">
Saved Jobs
</a>

<!-- ✅ NEW BUTTONS -->
<a href="?filter=interview"
class="px-5 py-2 rounded-full border <?php echo ($filter=='interview') ? 'bg-yellow-400 text-black' : 'border-gray-600'; ?>">
Interview Jobs
</a>

<a href="?filter=offered"
class="px-5 py-2 rounded-full border <?php echo ($filter=='offered') ? 'bg-yellow-400 text-black' : 'border-gray-600'; ?>">
Offered Jobs
</a>

</div>

<!-- JOB GRID -->
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">

<?php while($row = mysqli_fetch_assoc($result)) { 

    $seconds = $row['seconds_old'];

    if ($seconds < 60) $posted = "Just now";
    elseif ($seconds < 3600) $posted = floor($seconds/60)." min ago";
    elseif ($seconds < 86400) $posted = floor($seconds/3600)." hr ago";
    else $posted = floor($seconds/86400)." days ago";

    $saved = !empty($row['saved']);

    $logo = !empty($row['logo']) 
        ? "../company/uploads/".$row['logo'] 
        : "https://via.placeholder.com/70";

    $expired = strtotime($row['deadline']) < time();
?>

<!-- CARD -->
<div class="bg-[#161616] p-6 rounded-2xl border border-gray-800 hover:border-yellow-400 transition relative">

<!-- Company -->
<div class="flex items-center gap-4 mb-5">
<img src="<?php echo $logo; ?>" 
class="w-14 h-14 rounded-xl object-cover bg-white p-1">

<div>
<h3 class="text-lg font-semibold"><?php echo $row['title']; ?></h3>

<p class="text-gray-400 text-sm flex items-center gap-2">
<?php echo $row['cname']; ?> 
• 
<i class="fa-solid fa-users text-gray-500"></i>
<?php echo $row['applicant']; ?> Applicants
</p>
</div>
</div>

<!-- STATUS -->
<?php if(isset($row['offer_status'])){ ?>
<span class="text-yellow-400 text-xs font-semibold">
Offer: <?php echo ucfirst($row['offer_status']); ?>
</span>
<?php } elseif($expired) { ?>
<span class="text-red-400 text-xs font-semibold">Closed</span>
<?php } else { ?>
<span class="text-green-400 text-xs font-semibold">Open</span>
<?php } ?>

<!-- Tags -->
<div class="flex flex-wrap gap-2 text-xs mb-5 mt-2">
<span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
<?php echo $row['experience_required']; ?>
</span>

<span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
<?php echo $row['job_type']; ?>
</span>

<span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full">
<?php echo $row['location']; ?>
</span>
</div>

<!-- Description -->
<p class="text-gray-400 text-sm mb-6 overflow-hidden">
<?php echo substr($row['description'],0,100); ?>...
</p>

<!-- Salary + Time -->
<div class="flex justify-between items-center text-sm mb-3">
<div class="font-semibold text-white text-base">
₹ <?php echo $row['salary']; ?> LPA
</div>

<div class="text-gray-400 flex items-center gap-2">
<i class="fa-regular fa-clock text-yellow-400"></i>
<?php echo $posted; ?>
</div>
</div>

<!-- BUTTON -->
<?php if($expired) { ?>

<button class="block w-full text-center bg-gray-600 text-white py-3 rounded-xl font-semibold cursor-not-allowed">
Job Closed
</button>

<?php } else { ?>

<a href="job_details.php?jid=<?php echo $row['jid']; ?>"
class="block text-center bg-yellow-400 text-black py-3 rounded-xl font-semibold hover:bg-yellow-500 transition">
View Details
</a>

<?php } ?>

</div>

<?php } ?>

</div>
</div>

<?php include("../include/footer.php"); ?>

</body>
</html>