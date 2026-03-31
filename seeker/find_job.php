<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";

date_default_timezone_set('Asia/Kolkata');

$uid = $_SESSION['uid'] ?? 0;

/* ================= FILTER VALUES ================= */
$filter = $_GET['filter'] ?? '';
$title = $_GET['title'] ?? '';
$location_filter = $_GET['location'] ?? '';
$experience = $_GET['experience'] ?? '';
$company_filter = $_GET['company'] ?? '';
$salary = isset($_GET['salary']) ? (int)$_GET['salary'] : 100;

/* ================= BASE QUERY ================= */
$where = "WHERE job.deadline >= NOW()";

// Title filter
if(!empty($title)){
    $title = mysqli_real_escape_string($conn, $title);
    $where .= " AND job.title LIKE '%$title%'";
}

// Location filter
if(!empty($location_filter)){
    $loc = mysqli_real_escape_string($conn, $location_filter);
    $where .= " AND LOWER(job.location) LIKE LOWER('%$loc%')";
}

// Experience filter
if($experience !== ""){
    $where .= " AND job.experience_required='$experience'";
}

// Company filter
if(!empty($company_filter)){
    $where .= " AND job.cid='$company_filter'";
}

// Job Type filter
if(!empty($filter)){
    $where .= " AND job.job_type='$filter'";
}

// Salary filter
$where .= " AND job.salary <= $salary";

/* ================= QUERY ================= */
$sql = "SELECT job.*, company.cname, company.logo,
        EXISTS(
            SELECT 1 FROM saved_job 
            WHERE saved_job.jid = job.jid AND saved_job.uid = '$uid'
        ) AS saved,
        TIMESTAMPDIFF(SECOND, job.posted_at, NOW()) as seconds_old
        FROM job
        JOIN company ON job.cid = company.cid
        $where
        ORDER BY job.posted_at DESC";

$result = mysqli_query($conn, $sql);
if(!$result){
    die("SQL Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Find Jobs</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../dist/styles.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">

</head>

<body class="bg-black text-white min-h-screen overflow-x-hidden">
<?php include("../include/navbar.php"); ?>

<div class="max-w-7xl mx-auto px-6 mt-20 mb-10">

<h2 class="text-3xl text-center font-semibold mb-12">Recommended Jobs</h2>

<!-- ================= FILTER FORM ================= -->
<form method="GET" class="mb-8">
<div class="flex flex-wrap items-center gap-3 bg-[#161616] p-4 rounded-2xl border border-gray-800">

    <!-- Job Title -->
    <div class="flex items-center gap-2 bg-[#0f0f0f] border border-gray-700 px-3 h-10 rounded-lg flex-1 min-w-[140px]">
        <i class="fa-solid fa-magnifying-glass text-yellow-400 text-sm"></i>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Job Title"
        class="bg-transparent outline-none text-sm w-full text-white placeholder-gray-400">
    </div>

    <!-- Location -->
    <div class="flex items-center gap-2 bg-[#0f0f0f] border border-gray-700 px-3 h-10 rounded-lg flex-1 min-w-[140px]">
        <i class="fa-solid fa-location-dot text-yellow-400 text-sm"></i>
        <select name="location" onchange="this.form.submit()" class="bg-[#0f0f0f] text-white outline-none w-full text-sm appearance-none">
            <option value="">Location</option>
            <?php
            $locQ = mysqli_query($conn,"SELECT DISTINCT location FROM job WHERE location!=''");
            while($l = mysqli_fetch_assoc($locQ)){
            ?>
            <option value="<?php echo $l['location']; ?>" <?php if($location_filter==$l['location']) echo 'selected'; ?>>
                <?php echo $l['location']; ?>
            </option>
            <?php } ?>
        </select>
    </div>

    <!-- Experience -->
    <div class="flex items-center gap-2 bg-[#0f0f0f] border border-gray-700 px-3 h-10 rounded-lg flex-1 min-w-[140px]">
        <i class="fa-solid fa-briefcase text-yellow-400 text-sm"></i>
        <select name="experience" onchange="this.form.submit()" class="bg-[#0f0f0f] text-white outline-none w-full text-sm appearance-none">
            <option value="">Experience</option>
            <option value="Fresher" <?php if($experience=='Fresher') echo 'selected'; ?>>Fresher</option>
            <option value="Intermediate" <?php if($experience=='Intermediate') echo 'selected'; ?>>Intermediate</option>
            <option value="Expert" <?php if($experience=='Expert') echo 'selected'; ?>>Expert</option>
        </select>
    </div>

    <!-- Job Type -->
    <div class="flex items-center gap-2 bg-[#0f0f0f] border border-gray-700 px-3 h-10 rounded-lg flex-1 min-w-[140px]">
        <i class="fa-solid fa-file-lines text-yellow-400 text-sm"></i>
        <select name="filter" onchange="this.form.submit()" class="bg-[#0f0f0f] text-white outline-none w-full text-sm appearance-none">
            <option value="">Job Type</option>
            <option value="Full Time" <?php if($filter=='Full Time') echo 'selected'; ?>>Full Time</option>
            <option value="Part Time" <?php if($filter=='Part Time') echo 'selected'; ?>>Part Time</option>
            <option value="Remote" <?php if($filter=='Remote') echo 'selected'; ?>>Remote</option>
        </select>
    </div>

    <!-- Company -->
    <div class="flex items-center gap-2 bg-[#0f0f0f] border border-gray-700 px-3 h-10 rounded-lg flex-1 min-w-[140px]">
        <i class="fa-solid fa-building text-yellow-400 text-sm"></i>
        <select name="company" onchange="this.form.submit()" class="bg-[#0f0f0f] text-white outline-none w-full text-sm appearance-none">
            <option value="">Company</option>
            <?php
            $compQ = mysqli_query($conn,"SELECT cid, cname FROM company ORDER BY cname ASC");
            while($c = mysqli_fetch_assoc($compQ)){
            ?>
            <option value="<?php echo $c['cid']; ?>" <?php if($company_filter==$c['cid']) echo 'selected'; ?>>
                <?php echo $c['cname']; ?>
            </option>
            <?php } ?>
        </select>
    </div>

    <!-- Salary -->
    <div class="flex items-center gap-2 bg-[#0f0f0f] border border-gray-700 px-3 h-10 rounded-lg flex-1 min-w-[160px]">
        <i class="fa-solid fa-indian-rupee-sign text-yellow-400 text-sm"></i>
        <input type="range" id="salaryRange" name="salary" min="0" max="50" value="<?php echo $salary; ?>" class="w-full accent-yellow-400">
        <span class="text-yellow-400 text-xs whitespace-nowrap">₹ <span id="salaryVal"><?php echo $salary; ?></span> LPA</span>
    </div>

    <!-- Clear -->
    <a href="find_job.php" class="bg-yellow-400 text-black px-4 h-10 flex items-center justify-center rounded-lg text-sm font-semibold hover:bg-yellow-500 whitespace-nowrap">
        Clear
    </a>
</div>
</form>

<!-- ================= JOB CARDS ================= -->
<?php if(mysqli_num_rows($result) == 0){ ?>
<div class="flex flex-col items-center justify-center py-16">
    <div class="text-5xl mb-4">&#x1F614;</div>
    <p class="text-xl text-[#D7AE27] font-semibold">No Jobs Available</p>
</div>
<?php } ?>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
<?php while($row = mysqli_fetch_assoc($result)) { 
    $seconds = $row['seconds_old'];
    if ($seconds < 60) $posted = "Just now";
    elseif ($seconds < 3600) $posted = floor($seconds/60)." minutes ago";
    elseif ($seconds < 86400) $posted = floor($seconds/3600)." hours ago";
    elseif ($seconds < 172800) $posted = "1 day ago";
    else $posted = floor($seconds/86400)." days ago";

    $saved = !empty($row['saved']);
    $logo = !empty($row['logo']) ? "../company/uploads/".$row['logo'] : "https://via.placeholder.com/70";
?>
<div class="bg-[#161616] p-6 rounded-2xl border border-gray-800 hover:border-yellow-400 transition-all duration-300 relative">

    <!-- Save Button -->
    <?php if(isset($_SESSION['uid'])) { ?>
    <form method="POST" action="<?php echo $saved ? 'unsave_job.php' : 'save_job.php'; ?>" class="absolute top-4 right-4 z-50">
        <input type="hidden" name="jid" value="<?php echo $row['jid']; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <button type="submit" class="text-2xl">
            <i class="<?php echo $saved ? 'fa-solid fa-bookmark text-yellow-400' : 'fa-regular fa-bookmark text-white hover:text-yellow-400'; ?>"></i>
        </button>
    </form>
    <?php } ?>

    <!-- Company Info -->
    <div class="flex items-center gap-4 mb-5">
        <img src="<?php echo $logo; ?>" class="w-14 h-14 rounded-xl object-cover bg-white p-1">
        <div>
            <h3 class="text-lg font-semibold"><?php echo $row['title']; ?></h3>
            <p class="text-gray-400 text-sm flex items-center gap-2">
                <?php echo $row['cname']; ?> • <i class="fa-solid fa-users text-gray-500"></i> <?php echo $row['applicant']; ?> Applicants
            </p>
        </div>
    </div>

    <!-- Tags -->
    <div class="flex flex-wrap gap-2 text-xs mb-5">
        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full"><?php echo $row['experience_required']; ?></span>
        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full"><?php echo $row['job_type']; ?></span>
        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full"><?php echo $row['location']; ?></span>
    </div>

    <!-- Description -->
    <p class="text-gray-400 text-sm mb-6"><?php echo substr($row['description'],0,100); ?>...</p>

    <!-- Salary + Posted -->
    <div class="flex justify-between items-center text-sm mb-3">
        <div class="font-semibold text-white text-base">₹ <?php echo $row['salary']; ?> LPA</div>
        <div class="text-gray-400 flex justify-end items-center gap-2"><i class="fa-regular fa-clock text-yellow-400"></i> <?php echo $posted; ?></div>
    </div>

    <!-- View Button -->
    <a href="job_details.php?jid=<?php echo $row['jid']; ?>" class="block text-center bg-yellow-400 text-black py-3 rounded-xl font-semibold hover:bg-yellow-500 transition">
        View Details
    </a>
</div>
<?php } ?>
</div>

</div>

<script>
const salaryRange = document.getElementById("salaryRange");
const salaryVal = document.getElementById("salaryVal");
let timer;
salaryRange.addEventListener("input", () => {
    salaryVal.innerText = salaryRange.value;
    clearTimeout(timer);
    timer = setTimeout(() => { salaryRange.form.submit(); }, 400);
});
</script>

<?php include("../include/footer.php"); ?>
</body>
</html>