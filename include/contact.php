<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php"; // CSRF helper file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

// 🔐 Only logged-in users can contact
if(!isset($_SESSION['uid'])){
    header("Location: ../auth/login.php");
    exit();
}

// Get user info
$uid = $_SESSION['uid'];
$role = $_SESSION['role']; // company or seeker

$user_sql = "SELECT email, sname, cname FROM users 
             LEFT JOIN job_seeker ON users.uid=job_seeker.uid
             LEFT JOIN company ON users.uid=company.uid
             WHERE users.uid='$uid'";
$user_res = mysqli_query($conn, $user_sql);
if(mysqli_num_rows($user_res) == 0){
    die("User not found");
}
$user = mysqli_fetch_assoc($user_res);

// ✅ Admin / Career Craft email
$admin_email = "careercraft535@gmail.com";
$admin_name = "Career Craft";

$msg = "";

// Generate CSRF token
$csrf_token = generateCSRFToken();

// Handle form submission
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
        $mail->Username = 'careercraft535@gmail.com';  // admin email
        $mail->Password = 'twhx zekb bklj ceow';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($admin_email, 'Career Craft');
        $mail->addAddress($admin_email, $admin_name);
        $mail->addReplyTo($email);

        $mail->isHTML(true);
        $mail->Subject = "Contact by $email";

        $mail->Body = "
        <div style='font-family:Arial; background:white; padding:20px; color:black'>
            <h2 style='color:#facc15;'>New Message from $email</h2>
            <p><b>Email:</b> $email</p>
            <p><b>Message:</b></p>
            <div style='background:white; padding:10px; border-radius:5px'>
                $message
            </div>
        </div>
        ";

        $mail->send();
        $msg = "✅ Message sent successfully!";
        regenerateCSRFToken(); // new token after success
    }catch(Exception $e){
        $msg = "❌ Mail Error: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Career Craft | Contact Us</title>
<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="../image/logo3.jpg" type="image/png">
</head>

<body class="bg-black text-white flex flex-col min-h-screen">

<?php include("../include/navbar.php"); ?>

<!-- MAIN CONTENT -->
<div class="flex-grow flex flex-col justify-center px-4 py-10">

   <div class="flex justify-start mt-10">
      <a href="http://localhost/php_program/project/home.php"
         class="text-yellow-400 text-sm hover:underline">
         ← Back
      </a>
    </div>
  <h2 class="text-3xl md:text-4xl font-semibold text-[#D7AE27]  text-center">
            Contact
            <span class="relative inline-block text-white">
                Us
                <span class="absolute left-0 top-full mt-10 w-full h-1 bg-[#D7AE27] rounded-sm"></span>
            </span>
        </h2>

    <!-- Contact Card + Heading -->
    <div class="bg-[#1a1a1a] p-8 rounded-xl w-full max-w-md mx-auto shadow-lg mt-10">

     
        
        <h2 class="text-2xl text-yellow-400 font-semibold mb-6 text-center">
            Contact <span class="text-white">Career Craft</span>
        </h2>

        <form method="POST" class="space-y-4">
            <!-- CSRF token -->
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <input type="email" name="email" placeholder="Your Email" required
            class="w-full p-3 bg-black border border-gray-600 rounded"
            value="<?= htmlspecialchars($user['email']) ?>" readonly>

            <textarea name="message" placeholder="Your Message" required
            class="w-full p-3 bg-black border border-gray-600 rounded h-32"></textarea>

            <button name="submit"
            class="w-full bg-yellow-400 text-black py-3 rounded hover:bg-yellow-500 font-semibold">
            Send Message
            </button>
        </form>

        <?php if($msg): ?>
        <p class="mt-4 text-green-400 text-center"><?= $msg ?></p>
        <?php endif; ?>

    </div>

</div>

<!-- FOOTER -->
<div class="mt-auto">
<?php include("../include/footer.php"); ?>
</div>

</body>
</html>