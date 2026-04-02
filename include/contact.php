<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

// 🔐 Only logged-in users
if(!isset($_SESSION['uid'])){
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

$user_sql = "SELECT email FROM users WHERE uid='$uid'";
$user_res = mysqli_query($conn, $user_sql);

if(mysqli_num_rows($user_res) == 0){
    die("User not found");
}

$user = mysqli_fetch_assoc($user_res);

// Admin Email
$admin_email = "careercraft535@gmail.com";
$admin_name = "Career Craft";

$msg = "";
$csrf_token = generateCSRFToken();

// FORM SUBMIT
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
    
    if(!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])){
        die("Invalid CSRF token");
    }

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try{
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'careercraft535@gmail.com';
        $mail->Password = 'twhx zekb bklj ceow';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($admin_email, 'Career Craft');
        $mail->addAddress($admin_email, $admin_name);
        $mail->addReplyTo($email);

        $mail->isHTML(true);
        $mail->Subject = "Contact by $email";

        $mail->Body = "
        <div style='font-family:Arial; padding:20px;'>
            <h2 style='color:#facc15;'>New Message</h2>
            <p><b>Email:</b> $email</p>
            <p><b>Message:</b><br>$message</p>
        </div>
        ";

        $mail->send();
        $msg = "✅ Message sent successfully!";
        regenerateCSRFToken();

    }catch(Exception $e){
        $msg = "❌ Mail Error: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Contact Us</title>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 <link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white flex flex-col min-h-screen">

<?php include("../include/navbar.php"); ?>

<!-- MAIN -->
<div class="flex-grow px-4 py-10">

    <!-- BACK -->
    <div class="max-w-6xl mx-auto mb-6 mt-10">
            <a href="http://localhost/php_program/project/home.php" 
  class="text-yellow-400 hover:underline">← Back</a>
    </div>
    

    <!-- TITLE -->
    <h2 class="text-3xl text-center text-yellow-400 font-bold mb-10">
        Contact Us
    </h2>

    <!-- 2 COLUMN SECTION -->
    <section class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8">

        <!-- LEFT: GET IN TOUCH -->
        <!-- LEFT: GET IN TOUCH -->
<div class="bg-[#0d0d0d] border border-gray-800 rounded-2xl p-8 shadow-lg">

    <h3 class="text-2xl text-yellow-400 font-semibold mb-6">
        Get in Touch
    </h3>

    <p class="text-gray-400 mb-6 leading-relaxed">
        If you have any questions regarding job applications, account issues, or partnership opportunities, feel free to contact us.
    </p>

    <!-- ADDRESS -->
    <div class="mb-5">
        <h4 class="text-yellow-400 font-semibold">Address:</h4>
        <p class="text-gray-300 mt-1">
            123 Innovation Street,<br>
             Surat, Gujarat, India
        </p>
    </div>

    <!-- PHONE -->
    <div class="mb-5">
        <h4 class="text-yellow-400 font-semibold">Phone:</h4>
        <p class="text-gray-300 mt-1">
            +91 98765 67890
        </p>
    </div>

    <!-- EMAIL -->
    <div class="mb-5">
        <h4 class="text-yellow-400 font-semibold">Email:</h4>
        <p class="text-gray-300 mt-1">
            careercraft535@gmail.com
        </p>
    </div>

    <!-- WORKING HOURS -->
    <div>
        <h4 class="text-yellow-400 font-semibold">Working Hours:</h4>
        <p class="text-gray-300 mt-1">
            Monday – Friday : 9:00 AM – 6:00 PM
        </p>
    </div>

</div>

        <!-- RIGHT: CONTACT FORM -->
        <div class="bg-[#1a1a1a] p-6 rounded-xl shadow-lg">

            <h3 class="text-2xl text-yellow-400 mb-6 text-center">
                Send Message
            </h3>

            <form method="POST" class="space-y-4">

                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <input type="email" name="email"
                class="w-full p-3 bg-black border border-gray-600 rounded"
                value="<?= htmlspecialchars($user['email']) ?>" readonly>

                <textarea name="message" placeholder="Your Message"
                class="w-full p-3 bg-black border border-gray-600 rounded h-32"
                required></textarea>

                <button name="submit"
                class="w-full bg-yellow-400 text-black py-3 rounded hover:bg-yellow-500">
                Send Message
                </button>

            </form>

            <?php if($msg): ?>
                <p class="mt-4 text-green-400 text-center"><?= $msg ?></p>
            <?php endif; ?>

        </div>

    </section>

</div>

<?php include("../include/footer.php"); ?>

</body>
</html>