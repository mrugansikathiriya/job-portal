<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../phpmailer/src/Exception.php';
require __DIR__ . '/../phpmailer/src/PHPMailer.php';
require __DIR__ . '/../phpmailer/src/SMTP.php';

$message = "";
$step = 1;

/* ================= FORM HANDLING ================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }
    /* ===== SEND / RESEND OTP ===== */
    if (isset($_POST['send_otp']) || isset($_POST['resend_otp'])) {

        if (isset($_POST['send_otp'])) {
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $_SESSION['email'] = $email;
        } else {
            if (!isset($_SESSION['email'])) {
                $message = "Session expired. Please try again.";
                $step = 1;
                goto end;
            }
            $email = $_SESSION['email'];
        }

  $check = mysqli_query($conn, "SELECT uid, role FROM users WHERE email='$email'");

if (mysqli_num_rows($check) == 0) {
    $message = "Email not registered";
    $step = 1;
} else {

    $userData = mysqli_fetch_assoc($check);

    if ($userData['role'] == 'admin') {
        $message = "Admin password cannot be changed here.";
        $step = 1;
        goto end;
    }

    // ✅ SEND OTP HERE
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_time'] = time();
    $_SESSION['otp_verified'] = false;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'careercraft535@gmail.com';
        $mail->Password = 'twhx zekb bklj ceow';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('careercraft535@gmail.com', 'Career Craft');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'OTP Verification';
        $mail->Body = "Your OTP is <b>$otp</b>. Valid for 60 seconds.";

        $mail->send();

        $message = "OTP sent successfully";
        $step = 2;

    } catch (Exception $e) {
        $message = "OTP sending failed";
        $step = 1;
    }
}
    }

    /* ===== VERIFY OTP ===== */
    elseif (isset($_POST['verify_otp'])) {

        $otpEntered = $_POST['otp'];

        if (
            isset($_SESSION['otp'], $_SESSION['otp_time']) &&
            $otpEntered == $_SESSION['otp'] &&
            (time() - $_SESSION['otp_time'] <= 60)
        ) {
            $_SESSION['otp_verified'] = true;

            // Clear old OTP for security
            unset($_SESSION['otp']);
            unset($_SESSION['otp_time']);

            $message = "OTP verified successfully";
            $step = 3;

        } else {
            $message = "Invalid or expired OTP";
            $step = 2;
        }
    }

    /* ===== CHANGE PASSWORD ===== */
    elseif (isset($_POST['change_password'])) {

        if (empty($_SESSION['otp_verified'])) {
            $message = "OTP verification required";
            $step = 1;
        } else {

            $newPass = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];

            if ($newPass !== $confirm) {
                $message = "Passwords do not match";
                $step = 3;
            } elseif (strlen($newPass) < 6) {
                $message = "Minimum 6 characters required";
                $step = 3;
            } else {
$hashed = password_hash($newPass, PASSWORD_DEFAULT);
$email = $_SESSION['email'];

/* UPDATE PASSWORD */
mysqli_query($conn, 
    "UPDATE users SET password='$hashed' WHERE email='$email'");

/* Remove remember token */
mysqli_query($conn, 
    "UPDATE users SET remember_token=NULL WHERE email='$email'");

/* Delete remember cookie */
setcookie("remember_token", "", time() - 3600, "/");

/* Cleanup session */
unset($_SESSION['otp_verified']);
unset($_SESSION['email']);

session_destroy();

header("Location: login.php");
exit;
            }
        }
    }
}
end:
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Career Craft | Forgot Password</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="http://localhost/php_program/project/image/logo3.jpg" type="image/png"></head>

<body class="min-h-screen flex items-center justify-center bg-black text-white relative overflow-hidden">

<div class="p-8 rounded-2xl shadow-2xl w-96 border border-white/10 relative">

<a href="login.php" class="absolute left-4 top-4 text-yellow-400 text-sm hover:underline">← Back</a>

<h2 class="text-2xl font-bold text-center text-yellow-400 mb-6">
Forgot Password
</h2>

<?php if ($message): ?>
<div class="mb-4 text-center text-sm px-4 py-2 rounded
<?= strpos($message,'successfully')!==false ? 'bg-green-600' : 'bg-red-600' ?>">
<?= $message ?>
</div>
<?php endif; ?>


<!-- STEP 1 -->
<?php if ($step == 1): ?>
<form method="post" class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<input type="email" name="email" placeholder="Enter Email" required
 class="w-full bg-black border border-white/20 p-2 rounded">
<button name="send_otp"
 class="w-full bg-yellow-500 text-black py-2 rounded font-bold">
Send OTP
</button>
</form>
<?php endif; ?>


<!-- STEP 2 -->
<?php if ($step == 2): ?>
<form method="post" class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<input type="text" name="otp" placeholder="Enter OTP"
 class="w-full bg-black border border-white/20 p-2 rounded">

<p class="text-center text-yellow-400 text-sm">
OTP expires in <span id="countdown">60</span>s
</p>

<p id="otpExpiredMsg"
 class="hidden text-center text-red-400 text-sm font-semibold">
OTP is expired. Please resend OTP.
</p>

<button id="verifyBtn" name="verify_otp"
 class="w-full bg-blue-600 py-2 rounded font-bold">
Verify OTP
</button>

<button name="resend_otp"
 class="w-full bg-gray-700 py-2 rounded font-bold">
Resend OTP
</button>

</form>
<?php endif; ?>


<!-- STEP 3 -->
<?php if ($step == 3): ?>
<form method="post" class="space-y-4">
<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

<div class="relative">
    <input type="password" id="new_password" name="new_password"
        placeholder="New Password"
        class="w-full bg-black border border-white/20 p-2 rounded pr-10" required>

    <span id="toggleNew" onclick="togglePassword('new_password','iconNew')"
        class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400">
        <i id="iconNew" class="fa-solid fa-eye-slash"></i>
    </span>
</div>

<div class="relative">
    <input type="password" id="confirm_password" name="confirm_password"
        placeholder="Confirm Password"
        class="w-full bg-black border border-white/20 p-2 rounded pr-10" required>

    <span id="toggleConfirm" onclick="togglePassword('confirm_password','iconConfirm')"
        class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400">
        <i id="iconConfirm" class="fa-solid fa-eye-slash"></i>
    </span>
</div>

<button name="change_password"
 class="w-full bg-green-600 py-2 rounded font-bold">
Change Password
</button>

</form>
<?php endif; ?>

</div>

<?php
$remaining = 0;
if (isset($_SESSION['otp_time'])) {
    $remaining = max(0, 60 - (time() - $_SESSION['otp_time']));
}
?>

<script>
(function () {
  const countdown = document.getElementById("countdown");
  const expiredMsg = document.getElementById("otpExpiredMsg");
  const verifyBtn = document.getElementById("verifyBtn");

  if (!countdown) return;

  let timeLeft = <?= $remaining ?>;

  expiredMsg.classList.add("hidden");
  verifyBtn.disabled = false;
  verifyBtn.classList.remove("opacity-50", "cursor-not-allowed");

  if (timeLeft <= 0) {
    countdown.textContent = "Expired";
    expiredMsg.classList.remove("hidden");
    verifyBtn.disabled = true;
    verifyBtn.classList.add("opacity-50", "cursor-not-allowed");
    return;
  }

  countdown.textContent = timeLeft;

  const timer = setInterval(() => {
    timeLeft--;
    if (timeLeft <= 0) {
      clearInterval(timer);
      countdown.textContent = "Expired";
      expiredMsg.classList.remove("hidden");
      verifyBtn.disabled = true;
      verifyBtn.classList.add("opacity-50", "cursor-not-allowed");
    } else {
      countdown.textContent = timeLeft;
    }
  }, 1000);
})();

// 👁 Toggle password
function togglePassword(fieldId, iconId) {
    const pass = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);

    if (pass.type === "password") {
        pass.type = "text";
        icon.classList.add("fa-eye");
        icon.classList.remove("fa-eye-slash");
    } else {
        pass.type = "password";
        icon.classList.add("fa-eye-slash");
        icon.classList.remove("fa-eye");
    }
}
</script>

</body>
</html>