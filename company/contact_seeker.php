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

// 🔐 Only company allowed
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

$company_uid = $_SESSION['uid'];
$msg = "";
$job_title = "";

// ================= GET SEEKER =================
if(isset($_GET['sid'])){
    $sid = intval($_GET['sid']);

    $stmt = $conn->prepare("
        SELECT u.email, js.sname 
        FROM job_seeker js
        JOIN users u ON js.uid = u.uid
        WHERE js.sid = ?
    ");
    $stmt->bind_param("i", $sid);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
        $data = $res->fetch_assoc();
        $seeker_email = $data['email'];
        $seeker_name  = $data['sname'];
    } else {
        die("Seeker not found");
    }
} else {
    die("Invalid Request");
}

// ================= COMPANY INFO =================
$stmt2 = $conn->prepare("
    SELECT u.email, c.cname 
    FROM users u 
    JOIN company c ON u.uid = c.uid
    WHERE u.uid=?
");
$stmt2->bind_param("i", $company_uid);
$stmt2->execute();
$res2 = $stmt2->get_result();
$company_data = $res2->fetch_assoc();

$company_email = $company_data['email'];
$company_name  = $company_data['cname'];

// ================= COMPANY ID =================
$stmt3 = $conn->prepare("SELECT cid FROM company WHERE uid=?");
$stmt3->bind_param("i", $company_uid);
$stmt3->execute();
$res3 = $stmt3->get_result();
$cid = $res3->fetch_assoc()['cid'];

// ================= JOBS =================
$jobs = mysqli_query($conn, "
    SELECT jid, title, status, is_approve 
    FROM job 
    WHERE cid='$cid' 
      AND is_approve='approved'
    ORDER BY posted_at DESC
");
// ================= CSRF =================
$csrf_token = generateCSRFToken();

// ================= SEND =================
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])){

    if(!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])){
        die("Invalid CSRF Token");
    }

    $subject_input = htmlspecialchars($_POST['subject']);
    $message_raw = htmlspecialchars($_POST['message']);
    $message_nl = nl2br($message_raw);
    $jid = !empty($_POST['jid']) ? intval($_POST['jid']) : 0;

    $is_offer = false; // Flag to check if this is a job offer

    // ================= JOB & APPLICATION CHECK =================
    $job_title = "";
    if($jid > 0){
        // Get job info
        $job_q = mysqli_query($conn, "SELECT title, status FROM job WHERE jid='$jid'");
        $job_data = mysqli_fetch_assoc($job_q);
        $job_title = $job_data['title'] ?? "";
        $job_status = $job_data['status'] ?? "open";

        if($job_status == 'closed'){
            $msg = "❌ Job is closed. Cannot contact candidate.";
        } else {
            // Check if candidate applied
            $app_q = mysqli_query($conn, "SELECT status FROM application WHERE sid='$sid' AND jid='$jid'");
            $app_status = mysqli_num_rows($app_q) > 0 ? mysqli_fetch_assoc($app_q)['status'] : null;

            if($app_status === 'rejected'){
                $msg = "❌ Candidate rejected for this job.";
            } else {
                // ✅ Candidate eligible → insert into job_offers if not exists
                $check_offer = mysqli_query($conn, "SELECT * FROM job_offers WHERE cid='$cid' AND sid='$sid' AND jid='$jid'");
                if(mysqli_num_rows($check_offer) == 0){
                    $stmt = $conn->prepare("INSERT INTO job_offers (cid, sid, jid, message) VALUES (?, ?, ?, ?)");
                    if(!$stmt) die("Prepare failed: " . $conn->error);
                    $stmt->bind_param("iiis", $cid, $sid, $jid, $message_raw);
                    if(!$stmt->execute()) die("Insert failed: " . $stmt->error);
                }
                $is_offer = true;
            }
        }
    }

    // ================= PREPARE EMAIL =================
    if(empty($msg) || !$jid || $job_status != 'closed'){
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'careercraft535@gmail.com';
            $mail->Password = 'twhx zekb bklj ceow';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('careercraft535@gmail.com', $company_name . ' HR');
            $mail->addAddress($seeker_email, $seeker_name);
            $mail->addReplyTo($company_email, $company_name);

            $mail->isHTML(true);

            // Subject
            $subject = $is_offer ? "Job Offer from $company_name - $job_title" : $subject_input;

            // Body
            $mail->Body = "
            <div style='font-family:Segoe UI;background:#f4f6f9;padding:30px'>
                <div style='max-width:600px;margin:auto;background:#fff;border-radius:12px'>
                    <div style='background:#D7AE27;padding:15px;text-align:center;font-weight:bold;color:#000'>
                        $company_name
                    </div>
                    <div style='padding:25px;color:#333'>
                        <p>Hello <b>$seeker_name</b>,</p>
                        <p>" . ($is_offer ? 
                            "We are pleased to offer you a position at <b>$company_name</b>." : 
                            "$company_name has contacted you regarding an opportunity.") . "</p>
                        " . ($jid > 0 ? "<div style='background:#f9f9f9;padding:10px;margin:10px 0'><b>Position:</b> $job_title</div>" : "") . "
                        <p>$message_nl</p>
                        <br>
                        <p>Regards,<br><b>$company_name HR</b><br>$company_email</p>
                    </div>
                </div>
            </div>";

            $mail->Subject = $subject;
            $mail->send();

            $msg = $is_offer ? "✅ Job Offer sent successfully!" : "✅ Message sent successfully!";
            regenerateCSRFToken();

        } catch (Exception $e) {
            $msg = "❌ Mail Error: " . $mail->ErrorInfo;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Contact Seeker</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>
<body class="bg-black text-white flex flex-col min-h-screen">
<?php include("../include/navbar.php"); ?>
<div class="flex-grow flex flex-col justify-center px-4 py-10">
    <div class="flex justify-start mt-10 max-w-md mx-auto w-full">
        <a href="find_talent.php" class="text-yellow-400 text-sm hover:underline">← Back</a>
    </div>
    <div class="bg-[#1a1a1a] p-8 rounded-xl w-full max-w-md mx-auto shadow-lg mt-6">
        <h2 class="text-2xl text-yellow-400 mb-4">Contact <?= htmlspecialchars($seeker_name) ?></h2>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <!-- JOB DROPDOWN -->
            <select name="jid" class="w-full p-2 bg-black border border-gray-600 rounded focus:outline-none focus:border-yellow-400">
                <option value="">-- Select Job (Optional) --</option>
                            <?php while($job = mysqli_fetch_assoc($jobs)) { ?>
            <option value="<?= $job['jid'] ?>" 
            <?= $job['status']=='closed' ? 'disabled' : '' ?>>
            <?= htmlspecialchars($job['title']) ?> 
            <?= $job['status']=='closed' ? '(Closed)' : '' ?>
            </option>                <?php } ?>
            </select>
            <input type="text" name="subject" placeholder="Subject" class="w-full p-2 bg-black border border-gray-600 rounded focus:outline-none focus:border-yellow-400" required>
            <textarea name="message" placeholder="Write your message..." class="w-full p-2 bg-black border border-gray-600 rounded h-28 focus:outline-none focus:border-yellow-400" required></textarea>
            <button name="send" class="w-full bg-yellow-400 text-black p-2 rounded hover:bg-yellow-500 transition">Send</button>
        </form>
        <?php if($msg): ?>
            <p class="mt-4 text-green-400"><?= $msg ?></p>
        <?php endif; ?>
    </div>
</div>
<div class="mt-auto">
<?php include("../include/footer.php"); ?>
</div>
</body>
</html>