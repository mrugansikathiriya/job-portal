<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

// 🔐 Only seeker
if (!isset($_SESSION['uid']) || $_SESSION['role'] != 'seeker') {
    header("Location: ../auth/login.php");
    exit();
}
$csrf_token = generateCSRFToken();
$uid = mysqli_real_escape_string($conn, $_SESSION['uid']);

/* ================= GET SID ================= */
$getSeeker = mysqli_query($conn, "SELECT sid FROM job_seeker WHERE uid='$uid'");
$seeker = mysqli_fetch_assoc($getSeeker);

if (!$seeker) {
    die("Seeker not found");
}

$sid = $seeker['sid'];

/* ================= FETCH APPLICATIONS ================= */
$sql = "SELECT 
            a.aid,
            a.status,
            a.applied_at,
            a.resume,
            a.interview_date,
            a.interview_time,
            j.jid,
            j.title,
            j.location,
            j.salary,
            c.cname,
            c.logo
        FROM application a
        JOIN job j ON a.jid = j.jid
        JOIN company c ON j.cid = c.cid
        WHERE a.sid = '$sid'
        ORDER BY a.applied_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Career Craft | My Applications</title>
    <link href="../dist/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
    <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white">


    <?php include("../include/navbar.php"); ?>
    <a href="sdashboard.php"
    class="inline-block mt-20 text-yellow-400 text-sm hover:underline ml-10">
    ← Back
    </a>
    <div class="max-w-6xl mx-auto mt-10 px-4">
    <h1 class="text-3xl font-bold mb-6 flex items-center justify-center gap-2">
        <i class="fa-solid fa-file-lines text-yellow-400"></i>
        <span class="text-white">My</span>
        <span class="text-yellow-400">Applications</span>
    </h1>

        <?php if(mysqli_num_rows($result) > 0): ?>

            <div class="space-y-5">

            <?php while($row = mysqli_fetch_assoc($result)): ?>

                <div class="bg-[#0f0f0f] border border-white/10 rounded-2xl p-5 flex justify-between items-center hover:shadow-lg transition mb-6">

                    <!-- LEFT -->
                    <div class="flex items-center gap-4">

                        <img src="../company/uploads/<?php echo $row['logo']; ?>" 
                            class="w-14 h-14 rounded-lg border border-yellow-400 object-cover">

                        <div>
                            <a href="job_details.php?jid=<?php echo $row['jid']; ?>">
                            <h2 class="text-lg font-semibold text-yellow-300">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h2></a>
                            <p class="text-gray-300">
                                <?php echo htmlspecialchars($row['cname']); ?>
                            </p>

                            <p class="text-sm text-gray-400">
                                📍 <?php echo $row['location']; ?> | 💰 ₹<?php echo $row['salary']; ?> LPA
                            </p>

                            <p class="text-xs text-gray-500">
                                Applied: <?php echo date("d M Y", strtotime($row['applied_at'])); ?>
                            </p>

                            <!-- ✅ INTERVIEW INFO -->
                            <?php if(!empty($row['interview_date']) && !empty($row['interview_time'])): ?>
                                <p class="text-sm text-blue-400 mt-1">
                                    🗓 Interview: 
                                    <?php echo date("d M Y", strtotime($row['interview_date'])); ?> 
                                    at 
                                    <?php echo date("h:i A", strtotime($row['interview_time'])); ?>
                                </p>
                            <?php endif; ?>

                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="text-right space-y-2">

                        <?php
                            $status = strtolower($row['status']);
                            $color = "text-gray-400";

                            if($status == "pending") $color = "text-yellow-400";
                            if($status == "selected") $color = "text-green-400";
                            if($status == "shortlisted") $color = "text-grey-400";
                            if($status == "rejected") $color = "text-red-400";
                            if($status == "withdrawn") $color = "text-red-500";

                        ?>

                        <!-- ✅ STATUS TEXT ONLY -->
                        <p class="font-semibold <?php echo $color; ?>">
                            <?php echo ucfirst($status); ?>
                        </p>

                        <!-- BUTTONS -->
                        <div class="flex gap-2 justify-end">

                            <!-- VIEW -->
                            <a href="job_details.php?jid=<?php echo $row['jid']; ?>"
                            class="bg-[#D7AE27] text-black px-4 py-1 rounded-lg font-semibold hover:scale-105 transition">
                            View
                            </a>

                            <!-- RESUME -->
                            <?php if(!empty($row['resume'])): ?>
                            <a href="../seeker/uploads/<?php echo $row['resume']; ?>" target="_blank"
                            class="bg-blue-500 px-4 py-1 rounded-lg text-white hover:bg-blue-600">
                            Resume
                            </a>
                            <?php endif; ?>

                            <!-- WITHDRAW -->
                            <?php if($status == "pending"): ?>
                            <form method="POST" action="withdraw_app.php">
                                <input type="hidden" name="aid" value="<?php echo $row['aid']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <button type="submit"
                                    onclick="return confirm('Withdraw this application?')"
                                    class="bg-red-500 px-4 py-1 rounded-lg text-white hover:bg-red-600">
                                    Withdraw
                                </button>
                            </form>
                            <?php elseif($status == "withdrawn"): ?>
                    <p class="text-yellow-400 text-sm font-semibold flex items-center gap-2">
                        Application Withdrawn
                    </p>                     
                <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

            </div>

        <?php else: ?>

            <!-- EMPTY -->
            <div class="text-center mt-20">
                <p class="text-gray-400 text-lg">No applications found</p>

                <a href="find_job.php"
                class="inline-block mt-4 bg-[#D7AE27] text-black px-6 py-2 rounded-lg font-semibold hover:scale-105">
                🔍 Browse Jobs
                </a>
            </div>

        <?php endif; ?>

    </div>


    </body>
    <?php include("../include/footer.php"); ?>

</html>