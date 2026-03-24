<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php"; // CSRF helper

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

$company_uid = $_SESSION['uid'];
$msg = "";

// ✅ Fetch seeker info for GET display
$sid = intval($_GET['sid'] ?? 0);
$seeker_name = "";
$seeker_email = "";

if ($sid) {
    $sql = "SELECT u.email, js.sname 
            FROM job_seeker js
            JOIN users u ON js.uid = u.uid
            WHERE js.sid = $sid";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_assoc($res);
        $seeker_name = $data['sname'];
        $seeker_email = $data['email'];
    } else {
        die("Seeker not found");
    }
} else {
    die("Invalid Seeker ID");
}

// ✅ Get company email
$comp_sql = "SELECT email FROM users WHERE uid='$company_uid'";
$comp_res = mysqli_query($conn, $comp_sql);
$company_email = mysqli_fetch_assoc($comp_res)['email'];

// ✅ CSRF token for form
$csrf_token = generateCSRFToken();

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    // POST values
    $sid_post = intval($_POST['sid']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = nl2br(htmlspecialchars($_POST['message']));

    // Re-fetch seeker info to prevent manipulation
    $sql = "SELECT u.email, js.sname 
            FROM job_seeker js
            JOIN users u ON js.uid = u.uid
            WHERE js.sid = $sid_post";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_assoc($res);
        $seeker_email = $data['email'];
        $seeker_name = $data['sname'];
    } else {
        die("Seeker not found");
    }

    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'careercraft535@gmail.com';
        $mail->Password = 'twhx zekb bklj ceow';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($company_email, 'Company HR');
        $mail->addAddress($seeker_email, $seeker_name);
        $mail->addReplyTo($company_email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "
        <div style='font-family:Segoe UI;background:#f4f6f9;padding:30px'>
            <div style='max-width:600px;margin:auto;background:#fff;border-radius:12px'>

                <div style='background:#D7AE27;padding:15px;text-align:center;font-weight:bold'>
                    Career Craft
                </div>

                <div style='padding:25px'>
                    <p>Hello <b>$seeker_name</b>,</p>

                    <p style='margin-top:10px'>
                        $message
                    </p>

                    <br>

                    <p>
                        Regards,<br>
                        <b>Company Team</b><br>
                        <small>$company_email</small>
                    </p>
                       <hr class='my-3' style='border-color:#ccc'>
            <p style='color:#555'>
                If you have any queries, please contact us at 
                <a href='mailto:$company_email' style='color:#D7AE27'>
                    $company_email
                </a>.
            </p>
                </div>

            </div>
        </div>
        ";

        $mail->send();
        $msg = "✅ Message sent successfully!";
        regenerateCSRFToken(); // new token after success
    } catch (Exception $e) {
        $msg = "❌ Mail Error: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Contact Seeker</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>
<body class="bg-black text-white flex flex-col min-h-screen">

<?php include("../include/navbar.php"); ?>

<div class="flex-grow flex flex-col justify-center px-4 py-10">

    <!-- Back Link aligned right -->
    <div class="flex justify-start mt-10">
      <a href="find_talent.php"
         class="text-yellow-400 text-sm hover:underline">
         ← Back
      </a>
    </div>
  <h2 class="text-3xl md:text-4xl font-semibold text-[#D7AE27]  text-center">
            Contact
            <span class="relative inline-block text-white">
                Seeker
                <span class="absolute left-0 top-full mt-14 w-full h-1 bg-[#D7AE27] rounded-sm"></span>
            </span>
        </h2>
    <!-- Contact Form Card -->
    <div class="bg-[#1a1a1a] p-8 rounded-xl w-full max-w-md mx-auto shadow-lg mt-10">

        <h2 class="text-2xl text-yellow-400 mb-4">
            Contact to <?= htmlspecialchars($seeker_name) ?>
        </h2>

        <form method="POST" class="space-y-3">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="sid" value="<?= $sid ?>">

            <input type="text" name="subject" placeholder="Subject"
                class="w-full p-2 bg-black border border-gray-600 rounded" required>

            <textarea name="message" placeholder="Write your message..."
                class="w-full p-2 bg-black border border-gray-600 rounded h-28" required></textarea>

            <button name="send"
                class="w-full bg-yellow-400 text-black p-2 rounded hover:bg-yellow-500">
                Send Message
            </button>
        </form>

        <?php if($msg): ?>
        <p class="mt-4 text-green-400"><?= $msg ?></p>
        <?php endif; ?>
    </div>

</div>

<!-- FOOTER -->
<div class="mt-auto">
<?php include("../include/footer.php"); ?>
</div>

</body>
</html>