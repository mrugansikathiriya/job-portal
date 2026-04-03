<?php
require "../config/db.php";
require "admin_auth.php";
require "../authc/csrf.php";

// PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    if(isset($_POST['uid']) && is_numeric($_POST['uid'])) {

        $uid = intval($_POST['uid']);
        $action = $_POST['action'];

        // ================= GET USER EMAIL =================
        $res = mysqli_query($conn, "SELECT email, uname FROM users WHERE uid=$uid");
        if($row = mysqli_fetch_assoc($res)){
            $user_email = $row['email'];
            $username = $row['uname'];
        } else {
            $user_email = '';
        }

        // ================= UPDATE STATUS =================
        if($action === "block"){
            mysqli_query($conn, "UPDATE users SET status='blocked' WHERE uid=$uid");
            $subject = "Account Blocked - CareerCraft";
            $body = "Hello $username,<br><br>Your account has been blocked by the admin. Please contact support if you think this is a mistake.<br><br>Regards,<br>CareerCraft Team";
        } else {
            mysqli_query($conn, "UPDATE users SET status='active' WHERE uid=$uid");
            $subject = "Account Activated - CareerCraft";
            $body = "Hello $username,<br><br>Your account has been reactivated by the admin. You can now log in to your account.<br><br>Regards,<br>CareerCraft Team";
        }

        // ================= SEND EMAIL USING PHPMailer =================
        if(!empty($user_email)){
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
             $mail->Username = 'careercraft535@gmail.com';
            $mail->Password = 'twhx zekb bklj ceow';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

                //Recipients
                $mail->setFrom('carrercraft535@gmail.com', 'CareerCraft');
                $mail->addAddress($user_email, $username);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;

                $mail->send();
                // echo "Email sent to $user_email"; // optional debug

            } catch (Exception $e) {
                // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }

        header("Location: users.php");
        exit;
    }
}
?>