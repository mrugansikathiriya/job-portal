<?php
session_start();
require "../config/db.php";

date_default_timezone_set('Asia/Kolkata');

$uid = $_SESSION['uid'] ?? 0;

$sql = "SELECT job.*, company.cname, company.logo,
        saved_job.jid AS saved_job,
        TIMESTAMPDIFF(SECOND, job.posted_at, NOW()) as seconds_old
        FROM job
        JOIN company ON job.cid = company.cid
        LEFT JOIN saved_job 
            ON job.jid = saved_job.jid 
            AND saved_job.uid = '$uid'
        WHERE job.deadline >= CURDATE()
        ORDER BY job.posted_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Find Jobs</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-[#0f0f0f] text-white min-h-screen">
    <?php include("../include/navbar.php"); ?>

<div class="max-w-7xl mx-auto px-6 mt-20 mb-10">

<h2 class="text-3xl text-center font-semibold mb-12">Recommended Jobs</h2>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">

<?php while($row = mysqli_fetch_assoc($result)) { 

    // Posted time
    $seconds = $row['seconds_old'];

    if ($seconds < 60) {
        $posted = "Just now";
    } elseif ($seconds < 3600) {
        $posted = floor($seconds / 60) . " minutes ago";
    } elseif ($seconds < 86400) {
        $posted = floor($seconds / 3600) . " hours ago";
    } elseif ($seconds < 172800) {
        $posted = "1 day ago";
    } else {
        $posted = floor($seconds / 86400) . " days ago";
    }

    $saved = !empty($row['saved_job']);
$logo = !empty($row['logo']) 
        ? "../company/uploads/".$row['logo'] 
        : "https://via.placeholder.com/70";?>

<!-- Job Card -->
<div class="bg-[#161616] p-6 rounded-2xl border border-gray-800 hover:border-yellow-400 transition-all duration-300 relative">
    
    <!-- Save Button -->
   <?php if(isset($_SESSION['uid'])) { ?>

<a href="<?php echo $saved ? 'unsave_job.php' : 'save_job.php'; ?>?jid=<?php echo $row['jid']; ?>"
   class="absolute top-4 right-4 z-50 text-2xl">

    <i class="<?php echo $saved 
        ? 'fa-solid fa-bookmark text-yellow-400' 
        : 'fa-regular fa-bookmark text-white hover:text-yellow-400'; ?>">
    </i>

</a>

<?php } ?>
    <!-- Company Info -->
    <div class="flex items-center gap-4 mb-5">
        <img src="<?php echo $logo; ?>" 
        class="w-14 h-14 rounded-xl object-cover bg-white p-1">

        <div>
            <h3 class="text-lg font-semibold">
                <?php echo $row['title']; ?>
            </h3>

            <p class="text-gray-400 text-sm flex items-center gap-2">
                <?php echo $row['cname']; ?> 
                • 
                <i class="fa-solid fa-users text-gray-500"></i>
                <?php echo $row['applicant']; ?> Applicants
            </p>
        </div>
    </div>

    <!-- Tags -->
    <div class="flex flex-wrap gap-2 text-xs mb-5">
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
    <p class="text-gray-400 text-sm mb-6">
        <?php echo substr($row['description'],0,100); ?>...
    </p>

    <!-- Salary + Posted -->
    <div class="flex justify-between items-center text-sm mb-3">
        <div class="font-semibold text-white text-base">
            ₹ <?php echo $row['salary']; ?> LPA
        </div>

        <div class="text-gray-400 flex justify-end items-center gap-2">
            <i class="fa-regular fa-clock text-yellow-400"></i>
            <?php echo $posted; ?>
        </div>
    </div>

    <!-- View Button -->
    <a href="job_details.php?id=<?php echo $row['jid']; ?>"
    class="block text-center bg-yellow-400 text-black py-3 rounded-xl font-semibold hover:bg-yellow-500 transition">
        View Details
    </a>
     
</div>

<?php } ?>

</div>
</div>

</body>
<?php include("../include/footer.php"); ?>

</html>