<?php
session_start();
include("../config/db.php");
require "../authc/csrf.php";

if(!isset($_SESSION['uid']) ){
    header("Location: ../auth/login.php");
    exit();
}
if(!isset($_GET['jid'])){
    header("Location: find_job.php");
    exit();
}

$jid = intval($_GET['jid']);

$sql = "SELECT job.*, 
        company.cname, 
        company.logo, 
        company.website, 
        company.location AS company_location,
        company.description AS company_desc
        FROM job
        JOIN company ON job.cid = company.cid
        WHERE job.jid=$jid";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result) == 0){
    echo "Job not found";
    exit();
}

$row = mysqli_fetch_assoc($result);

$saved = false;
$uid = $_SESSION['uid'];

$check = mysqli_query($conn,
"SELECT 1 FROM saved_job 
 WHERE uid='$uid' AND jid='".$row['jid']."'");

if(mysqli_num_rows($check) > 0){
    $saved = true;
}

$applied = false;
$pendingTest = false;
$aid = 0;

$applyCheck = mysqli_query($conn,
"SELECT aid, score FROM application 
 WHERE uid='$uid' AND jid='".$row['jid']."' 
 LIMIT 1");

if(mysqli_num_rows($applyCheck) > 0){
    $appData = mysqli_fetch_assoc($applyCheck);
    $aid = $appData['aid'];

    if($appData['score'] == 0){
        $pendingTest = true;
    }

    $applied = true;
}

$logo = !empty($row['logo']) 
? "../company/uploads/".$row['logo'] 
: "https://via.placeholder.com/70";

$list_sql = "SELECT job.*, company.cname, company.logo
FROM job
JOIN company ON job.cid = company.cid";

$list_result = mysqli_query($conn,$list_sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Career craft | Job details</title>
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

<!-- ✅ MAIN FULL WIDTH FLEX -->
<div class="w-full mt-5 mb-10 grid grid-cols-1 lg:grid-cols-10 gap-6 px-6">
            <!-- ================= LEFT (70%) ================= -->
    <div class="lg:col-span-7">
           <!-- ❌ IMPORTANT: removed max-w-4xl mx-auto -->
        <div class="w-full bg-[#1a1a1a] p-8 rounded-2xl border border-gray-800">

            <div class="flex justify-between items-start">

                <div class="flex items-center gap-4">
                    <img src="<?php echo $logo; ?>" class="w-16 h-16 rounded-lg bg-white p-1">

                    <div>
                        <h2 class="text-2xl font-semibold"><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p class="text-gray-400"><?php echo $row['cname']; ?></p>
                    </div>
                </div>

                <form method="POST" action="<?php echo $saved ? 'unsave_job.php' : 'save_job.php'; ?>">
                    <input type="hidden" name="jid" value="<?php echo $row['jid']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <button class="text-2xl <?php echo $saved ? 'text-yellow-400' : 'text-gray-400'; ?>">
                        <i class="<?php echo $saved ? 'fa-solid' : 'fa-regular'; ?> fa-bookmark"></i>
                    </button>
                </form>

            </div>

            <div class="mt-8 space-y-3 text-gray-300">
                <p><b>Location:</b> <?php echo $row['location']; ?></p>
                <p><b>Experience:</b> <?php echo $row['experience_required']; ?></p>
                <p><b>Job Type:</b> <?php echo $row['job_type']; ?></p>
                <p><b>Work Mode:</b> <?php echo $row['work_mode']; ?></p>
                <p><b>Salary:</b> ₹ <?php echo $row['salary']; ?></p>

                <p class="text-red-400">
                <b>Deadline:</b> <?php echo date("d M Y", strtotime($row['deadline'])); ?>
                </p>
<p><b>Total Applicants:</b> <?php echo $row['applicant']; ?></p>
            </div>

            <hr class="my-8 border-gray-700">

            <h3 class="text-xl font-semibold mb-3">Job Description</h3>
            <p class="text-gray-400 mb-6">
            <?php echo nl2br($row['description']); ?>
            </p>

            <button onclick="toggleCompany(this)"
            class="bg-gray-800 border border-yellow-400 text-yellow-400 px-5 py-2 rounded-lg">
            View Company Details
            </button>

            <div id="companySection" class="hidden mt-4">
                <p><b>Name:</b> <?php echo $row['cname']; ?></p>
                <p><b>Location:</b> <?php echo $row['company_location']; ?></p>

                <?php if(!empty($row['website'])){ ?>
                <p><b>Website:</b> 
                <a href="<?php echo $row['website']; ?>" target="_blank" class="text-yellow-400 underline">
                Visit</a></p>
                <?php } ?>

                <p class="text-gray-400 mt-3">
                <?php echo nl2br($row['company_desc']); ?>
                </p>
            </div>

            <?php if($pendingTest){ ?>
            <a href="test.php?aid=<?=$aid?>" class="block mt-6 bg-yellow-400 text-black text-center py-2 rounded-lg">Continue Test</a>

            <?php } elseif(!$applied){ ?>
            <a href="apply_job.php?jid=<?=$row['jid']?>" class="block mt-6 bg-yellow-400 text-black text-center py-2 rounded-lg">Apply Now</a>

            <?php } else { ?>
            <button class="mt-6 w-full bg-gray-600 py-2 rounded-lg">Application Submitted</button>
            <?php } ?>

        </div>
    </div>

    <!-- ================= RIGHT (30%) ================= -->
    <div class="lg:col-span-3 h-[calc(100vh-100px)] overflow-y-auto sticky top-20">
            <?php while($job = mysqli_fetch_assoc($list_result)){ 

        $logo2 = !empty($job['logo']) 
        ? "../company/uploads/".$job['logo'] 
        : "https://via.placeholder.com/70";
        ?>

        <a href="?jid=<?php echo $job['jid']; ?>">

        <div class="bg-[#161616] p-5 rounded-xl border mb-4
        <?php echo ($job['jid']==$jid)?'border-yellow-400 bg-[#222]':'border-gray-800'; ?>">

            <div class="flex items-center gap-3 mb-3">
                <img src="<?php echo $logo2; ?>" class="w-12 h-12 rounded bg-white p-1">

                <div>
                    <h3 class="text-sm font-semibold"><?php echo $job['title']; ?></h3>
                    <p class="text-gray-400 text-xs"><?php echo $job['cname']; ?></p>
                </div>
            </div>

            <p class="text-gray-400 text-xs mb-2">
            <?php echo substr($job['description'],0,60); ?>...
            </p>

            <div class="text-xs text-yellow-400">
            ₹ <?php echo $job['salary']; ?> LPA
            </div>

        </div>
        </a>

        <?php } ?>

    </div>

</div>

<script>
function toggleCompany(btn){
let sec = document.getElementById("companySection");

if(sec.classList.contains("hidden")){
sec.classList.remove("hidden");
btn.innerText="Hide Company Details";
}else{
sec.classList.add("hidden");
btn.innerText="View Company Details";
}
}
</script>

</body>
</html>