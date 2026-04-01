<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../PHPMailer/src/PHPMailer.php";
require "../PHPMailer/src/SMTP.php";
require "../PHPMailer/src/Exception.php";

// 🔐 Only company
if (!isset($_SESSION['uid']) || $_SESSION['role'] != 'company') {
    header("Location: ../auth/login.php");
    exit();
}

$aid = intval($_GET['aid'] ?? 0);
$msg = "";
$company_uid = $_SESSION['uid'];

// CSRF token
$csrf_token = generateCSRFToken();

// ---------------- Fetch applicant & company info ----------------
$sql = "SELECT a.*, js.*, u.email AS seeker_email, u.contact AS contact, j.title AS job_title, 
        c.cname AS cname, cu.email AS company_email
        FROM application a
        JOIN job_seeker js ON js.sid = a.sid
        JOIN users u ON u.uid = js.uid
        JOIN job j ON j.jid = a.jid
        JOIN company c ON c.cid = j.cid
        JOIN users cu ON cu.uid = c.uid
        WHERE a.aid='$aid' AND c.uid='$company_uid'";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) == 0){
    die("Unauthorized access or applicant not found");
}
$data = mysqli_fetch_assoc($result);
// ---------------- STATUS DISPLAY ----------------
$display_status = $data['status'];

if($data['status'] == 'selected' && empty($data['interview_date'])){
    $display_status = "Shortlisted";
}
elseif(!empty($data['interview_date'])){
    $display_status = "Interview Scheduled";
}

// ---------------- PHPMailer Function ----------------
function sendSeekerEmail($seeker_email, $seeker_name, $subject, $message, $company_name, $company_email){
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'careercraft535@gmail.com';
        $mail->Password = 'twhx zekb bklj ceow'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('careercraft535@gmail.com', $company_name.' HR');
        $mail->addAddress($seeker_email, $seeker_name);
        $mail->addReplyTo($company_email, $company_name.' HR');

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "
        <div style='font-family:Segoe UI;background:#f4f6f9;padding:30px'>
            <div style='max-width:600px;margin:auto;background:#fff;border-radius:12px'>
                <div style='background:#D7AE27;padding:15px;text-align:center;font-weight:bold'>{$company_name}</div>
                <div style='padding:25px'>
                    <p>Hello <b>{$seeker_name}</b>,</p>
                    <p style='margin-top:10px'>{$message}</p>
                    <br>
                    <p>Regards,<br><b>{$company_name}</b> Team<br><small>{$company_email}</small></p>
                    <hr class='my-3' style='border-color:#ccc'>
                    <p style='color:#555'>If you have any queries, contact us at 
                        <a href='mailto:{$company_email}' style='color:#D7AE27'>{$company_email}</a>.
                    </p>
                </div>
            </div>
        </div>";
        $mail->send();
        return "✅ Email sent successfully!";
    } catch (Exception $e){
        return "❌ Mail Error: " . $mail->ErrorInfo;
    }
}

// ---------------- Handle Accept / Reject ----------------
if(isset($_POST['action'])){
    if(!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])){
        die("Invalid CSRF Token");
    }

    $status = mysqli_real_escape_string($conn, $_POST['action']);
    if($data['status'] == 'pending'){
        mysqli_query($conn,"UPDATE application SET status='$status' WHERE aid='$aid'");
        regenerateCSRFToken();

        if($status == 'selected'){
            $subject = "Congratulations! You are selected for {$data['job_title']}";
            $message_body = "You have been <b>selected</b> for the position <b>{$data['job_title']}</b>. Our HR will contact you for interview scheduling.";
            $msg = sendSeekerEmail($data['seeker_email'], $data['sname'], $subject, $message_body, $data['cname'], $data['company_email']);
        } elseif($status == 'rejected'){
            $subject = "Application Update for {$data['job_title']}";
            $message_body = "We regret to inform you that your application for the position <b>{$data['job_title']}</b> was not successful.";
            $msg = sendSeekerEmail($data['seeker_email'], $data['sname'], $subject, $message_body, $data['cname'], $data['company_email']);
        }

        header("Location: applicant_detail.php?aid=".$aid);
        exit();
    }
}

// ---------------- Handle Schedule Interview ----------------
if(isset($_POST['schedule'])){
    if(!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])){
        die("Invalid CSRF Token");
    }

    $date = mysqli_real_escape_string($conn,$_POST['date']);
    $time = mysqli_real_escape_string($conn,$_POST['time']);

    $today = date("Y-m-d");
    $current_time = date("H:i");

    if(empty($date) || empty($time)){
        $msg = "❌ Please select both date and time.";
    }
    elseif($date < $today){
        $msg = "❌ Cannot select past date.";
    }
    else {

        $existing_date = $data['interview_date'];
        $existing_time = $data['interview_time'];

        // ❌ Block update
        if(!empty($existing_date) && $existing_date < $today){
            $msg = "❌ Interview date expired.";
        }
        elseif(!empty($existing_date) && $existing_date == $today && $existing_time < $current_time){
            $msg = "❌ Interview time already passed.";
        }
        else {

    $isReschedule = !empty($data['interview_date']);

mysqli_query($conn,"UPDATE application 
SET interview_date='$date', 
    interview_time='$time',
    status='interview_scheduled'
WHERE aid='$aid'");

            regenerateCSRFToken();

if($isReschedule){
    $subject = "Interview Rescheduled for {$data['job_title']}";
    $message_body = "Your interview has been <b>rescheduled</b>.<br><br>
                     New Date: <b>$date</b><br>
                     Time: <b>$time</b>";
} else {
    $subject = "Interview Scheduled for {$data['job_title']}";
    $message_body = "Your interview for <b>{$data['job_title']}</b> is scheduled on <b>$date</b> at <b>$time</b>.";
}

            $msg = sendSeekerEmail(
                $data['seeker_email'],
                $data['sname'],
                $subject,
                $message_body,
                $data['cname'],
                $data['company_email']
            );

            header("Location: applicant_detail.php?aid=".$aid);
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Applicant Details | Career Craft</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">

 <style>
            /* Make calendar icon white */
            input[type="date"]::-webkit-calendar-picker-indicator {
                filter: invert(1);
                cursor: pointer;
            }
             input[type="time"]::-webkit-calendar-picker-indicator {
                filter: invert(1);
                cursor: pointer;
            }
            </style>

</head>
<body class="bg-black text-white min-h-screen flex flex-col">

<?php include("../include/navbar.php"); ?>

<div class="flex justify-start px-6 mt-16">
    <a href="view_applicant.php" class="text-yellow-400 text-sm hover:underline">← Back</a>
</div>

<div class="max-w-7xl mx-auto py-12 px-6 space-y-8">
    <h2 class="text-3xl text-yellow-400 font-bold mb-8 text-center">Applicant Details</h2>

    <!-- Card -->
    <div class="bg-[#1a1a1a] border border-gray-700 rounded-3xl p-10 shadow-xl space-y-6 w-full max-w-6xl mx-auto">

        <!-- Profile -->
        <div class="flex items-center gap-6">
            <?php if(!empty($data['profile_image'])): ?>
                <img src="../seeker/uploads/<?= htmlspecialchars($data['profile_image']) ?>"
                     class="w-24 h-24 rounded-full object-cover border-2 border-yellow-400">
            <?php else: ?>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($data['sname']) ?>&background=D7AE27&color=000"
                     class="w-24 h-24 rounded-full border-2 border-yellow-400">
            <?php endif; ?>
            <div>
                <h3 class="text-2xl text-yellow-400 font-semibold"><?= htmlspecialchars($data['sname']); ?></h3>
                <p class="text-gray-400"><?= htmlspecialchars($data['seeker_email']); ?></p>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div><span class="text-yellow-400 font-semibold">Contact</span><p><?= htmlspecialchars($data['contact']); ?></p></div>
            <div><span class="text-yellow-400 font-semibold">Education</span><p><?= htmlspecialchars($data['education']); ?></p></div>
            <div><span class="text-yellow-400 font-semibold">Experience</span><p><?= htmlspecialchars($data['experience']); ?> Years</p></div>
            <div><span class="text-yellow-400 font-semibold">Skills</span><p><?= htmlspecialchars($data['skillname']); ?></p></div>
            <div class="col-span-1 md:col-span-2"><span class="text-yellow-400 font-semibold">Bio</span><p><?= htmlspecialchars($data['bio']); ?></p></div>
            <div><span class="text-yellow-400 font-semibold">Score</span><p><?= htmlspecialchars($data['score']); ?></p></div>
            <div><span class="text-yellow-400 font-semibold">Current Status</span>
            <p class="<?php 
                if($display_status == 'Interview Scheduled'){ echo 'text-green-400'; }
                elseif($data['status']=='selected'){ echo 'text-green-400'; }
                elseif($data['status']=='rejected'){ echo 'text-red-400'; }
                else{ echo 'text-yellow-400'; } 
            ?>">
                <?= htmlspecialchars($display_status); ?>
            </p>
           </div>
        </div>

        <!-- Resume -->
        <div>
            <a href="../seeker/uploads/<?= htmlspecialchars($data['resume']) ?>" target="_blank" class="text-yellow-400 underline">
                View Resume
            </a>
        </div>

        <!-- Accept / Reject -->
        <?php if($data['status'] == 'pending'): ?>
            <form method="POST" class="flex gap-4 mt-6">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <button type="submit" name="action" value="selected"
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 rounded-2xl font-semibold transition-all">
                    Accept
                </button>
                <button type="submit" name="action" value="rejected"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white py-3 rounded-2xl font-semibold transition-all">
                    Reject
                </button>
            </form>
        <?php elseif($data['status'] == 'selected' || $data['status'] == 'interview_scheduled'): ?>

<div class="bg-gray-800/70 border-l-4 border-yellow-400 rounded-xl p-6 mt-6 shadow-md">
    <p class="text-yellow-400 font-bold flex items-center gap-2">
        <i class="fa-solid fa-calendar-check"></i> Interview
    </p>

    <?php if(!empty($data['interview_date']) && !empty($data['interview_time'])): ?>

        <!-- ✅ SHOW DETAILS -->
        <p class="text-green-400 mt-2 font-semibold">✅ Interview Scheduled</p>
        <p>Date: <span class="text-white"><?= htmlspecialchars($data['interview_date']); ?></span></p>
        <p>Time: <span class="text-white"><?= htmlspecialchars($data['interview_time']); ?></span></p>

        <?php
        $today = date("Y-m-d");
        $current_time = date("H:i");

        $canReschedule = false;

        if($data['interview_date'] > $today){
            $canReschedule = true;
        } elseif($data['interview_date'] == $today && $data['interview_time'] > $current_time){
            $canReschedule = true;
        }
        ?>

        <?php if($canReschedule): ?>

        <!-- ✅ RESCHEDULE FORM -->
        <form method="POST" class="mt-4 flex flex-col gap-3">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="flex gap-2">
                <input type="date" name="date" required 
                min="<?= date('Y-m-d') ?>"
                class="flex-1 p-3 rounded bg-black border border-gray-600">

                <input type="time" name="time" required 
                class="flex-1 p-3 rounded bg-black border border-gray-600">
            </div>

            <button type="submit" name="schedule"
                class="w-full bg-yellow-500 hover:bg-yellow-600 text-black py-3 rounded-2xl font-semibold">
                Reschedule Interview
            </button>
        </form>

        <?php else: ?>
            <p class="text-red-400 mt-2">⛔ Cannot reschedule (time passed)</p>
        <?php endif; ?>

    <?php else: ?>

        <!-- ✅ FIRST TIME SCHEDULE -->
        <form method="POST" class="mt-4 flex flex-col gap-3">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <div class="flex gap-2">
                <input type="date" name="date" required 
                min="<?= date('Y-m-d') ?>"
                class="flex-1 p-3 rounded bg-black border border-gray-600">

                <input type="time" name="time" required 
                class="flex-1 p-3 rounded bg-black border border-gray-600">
            </div>

            <?php if($msg): ?>
                <p class="text-sm text-red-400"><?= $msg ?></p>
            <?php endif; ?>

            <button type="submit" name="schedule"
                class="w-full bg-yellow-500 hover:bg-yellow-600 text-black py-3 rounded-2xl font-semibold">
                Schedule Interview
            </button>
        </form>

    <?php endif; ?>
</div>

<?php endif; ?>
    </div>
</div>

<?php include("../include/footer.php"); ?>
</body>
</html>