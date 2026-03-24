<?php
    session_start();
require "../config/db.php";

    if(!isset($_SESSION['uid'])){
        header("Location: login.php");
        exit();
    }

    if(!isset($_GET['jid'])){
        header("Location: cdashboard.php");
        exit();
    }

    $jid = intval($_GET['jid']);
    $uid = $_SESSION['uid'];

    /* ================= VERIFY JOB BELONGS TO COMPANY ================= */
    $check = mysqli_query($conn,"
    SELECT job.jid 
    FROM job
    JOIN company ON company.cid = job.cid
    WHERE job.jid='$jid' AND company.uid='$uid'
    ");
    if(mysqli_num_rows($check) == 0){
        die("Unauthorized access");
    }

    /* ================= FILTER ================= */
    $filter = $_GET['filter'] ?? 'all';

    if($filter == 'selected'){  
        $sql = "SELECT application.*, job_seeker.sname, job_seeker.profile_image,
                users.email, users.contact
                FROM application
                JOIN job_seeker ON job_seeker.sid = application.sid
                JOIN users ON users.uid = job_seeker.uid
                WHERE application.jid='$jid' AND LOWER(application.status)='selected'
                ORDER BY application.aid DESC";
    }
    elseif($filter == 'interview'){  
        $sql = "SELECT application.*, job_seeker.sname, job_seeker.profile_image,
                users.email, users.contact
                FROM application
                JOIN job_seeker ON job_seeker.sid = application.sid
                JOIN users ON users.uid = job_seeker.uid
                WHERE application.jid='$jid' AND application.interview_date IS NOT NULL
                ORDER BY application.aid DESC";
    }
    else{  
        $sql = "SELECT application.*, job_seeker.sname, job_seeker.profile_image,
                users.email, users.contact
                FROM application
                JOIN job_seeker ON job_seeker.sid = application.sid
                JOIN users ON users.uid = job_seeker.uid
                WHERE application.jid='$jid'
                ORDER BY application.aid DESC";
    }

    $result = mysqli_query($conn, $sql);
    if(!$result){ die(mysqli_error($conn)); }
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

        <!-- BACK BUTTON -->
        <div class="flex justify-start px-6 mt-16">
            <a href="http://localhost/php_program/project/company/total_applicant.php" class="text-yellow-400 text-sm hover:underline">← Back</a>
        </div>

        <div class="max-w-6xl mx-auto py-12 px-6">

        <h2 class="text-3xl font-semibold text-center mb-10 text-[#D7AE27]">
        Applicants
        </h2>

        <!-- FILTER BUTTONS -->
        <div class="flex justify-center gap-4 mb-6 flex-wrap">
            <a href="view_applicant.php?jid=<?= $jid ?>&filter=all"
            class="px-6 py-2 rounded-lg text-sm transition <?= ($filter=='all')?'bg-yellow-400 text-black':'border border-yellow-400 text-[#D7AE27] hover:bg-yellow-400 hover:text-black'; ?>">
            All Applicants
            </a>

            <a href="view_applicant.php?jid=<?= $jid ?>&filter=selected"
            class="px-6 py-2 rounded-lg text-sm transition <?= ($filter=='selected')?'bg-yellow-400 text-black':'border border-yellow-400 text-[#D7AE27] hover:bg-yellow-400 hover:text-black'; ?>">
            Selected Applicants
            </a>

            <a href="view_applicant.php?jid=<?= $jid ?>&filter=interview"
            class="px-6 py-2 rounded-lg text-sm transition <?= ($filter=='interview')?'bg-yellow-400 text-black':'border border-yellow-400 text-[#D7AE27] hover:bg-yellow-400 hover:text-black'; ?>">
            Short-listed Applicants
            </a>
        </div>

        <!-- MAIN CARD -->
        <div class="bg-[#1a1a1a] rounded-xl border border-gray-800 p-6">

        <?php if(mysqli_num_rows($result)==0){ ?>
        <div class="flex flex-col items-center justify-center py-16">
            <div class="text-5xl mb-4">😔</div>
            <p class="text-xl text-[#D7AE27] font-semibold">
                <?= ($filter=='selected')?'No Selected Applicants':'No Applicants'; ?>
            </p>
            <p class="text-gray-400 text-sm mt-2">No data available for this Job.</p>
        </div>
        <?php } ?>

        <?php while($row=mysqli_fetch_assoc($result)){ ?>
        <div class="bg-black/50 border border-gray-700 rounded-2xl p-6 mb-6 hover:border-yellow-400 transition">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">

                <!-- Avatar -->
                <?php if(!empty($row['profile_image'])): ?>
                <img src="../seeker/uploads/<?= htmlspecialchars($row['profile_image']); ?>"
                    class="w-14 h-14 rounded-full object-cover border border-[#D7AE27]">
                <?php else: ?>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['sname']); ?>&background=D7AE27&color=000"
                    class="w-14 h-14 rounded-full border border-[#D7AE27]">
                <?php endif; ?>

                <!-- Name & Email -->
                <div>
                    <h3 class="text-lg font-semibold"><?= htmlspecialchars($row['sname']); ?></h3>
                    <p class="text-gray-400 text-sm"><?= htmlspecialchars($row['email']); ?></p>
                </div>

            </div>

            <!-- STATUS BADGE -->
            <?php
            $status = strtolower($row['status']);
            $badgeClass = $status=='selected' ? 'bg-green-500/20 text-green-400' : ($status=='rejected'?'bg-red-500/20 text-red-400':'bg-yellow-500/20 text-yellow-400');
            $badgeText = !empty($row['interview_date']) ? 'Interview Scheduled' : ucfirst($status=='pending'?'Pending':$status);
            ?>
            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
                <?= $badgeText ?>
            </span>
        </div>

        <!-- Info Tags -->
        <div class="flex flex-wrap gap-3 mt-4">
            <span class="bg-yellow-500/20 text-[#D7AE27] px-3 py-1 rounded-full text-xs">Contact : <?= htmlspecialchars($row['contact']); ?></span>
            <span class="bg-yellow-500/20 text-[#D7AE27] px-3 py-1 rounded-full text-xs">Score : <?= htmlspecialchars($row['score']); ?></span>
            <?php if(!empty($row['interview_date'])): ?>
            <p class="text-green-400 text-sm">Interview : <?= $row['interview_date']; ?> <?= $row['interview_time']; ?></p>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="mt-4 flex items-center justify-between">
            <a href="uploads/<?= htmlspecialchars($row['resume']); ?>" target="_blank" class="text-[#D7AE27] underline text-sm">View Resume</a>
            <a href="applicant_detail.php?aid=<?= $row['aid']; ?>" class="bg-yellow-400 hover:bg-yellow-500 text-black px-5 py-2 rounded-lg font-medium transition">
                View Details
            </a>
        </div>

        </div>
        <?php } ?>

        </div>
        </div>

        <?php include("../include/footer.php"); ?>
        </body>
</html>