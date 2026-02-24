<?php
session_start();
require "../config/db.php";

$email = $password = "";
$emailErr = $passwordErr = "";
$toastError = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // ✅ SERVER VALIDATION (REFERENCE METHOD)
    if ($email == "")
        $emailErr = "Email required";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $emailErr = "Invalid email";

    if ($password == "")
        $passwordErr = "Password required";
    elseif (strlen($password) < 6)
        $passwordErr = "Minimum 6 characters";

    if ($emailErr=="" && $passwordErr=="") {

       $res = mysqli_query($conn,
    "SELECT * FROM users WHERE email='$email'"
);

if (mysqli_num_rows($res) == 1) {

    $row = mysqli_fetch_assoc($res);

    // ✅ Check if blocked
    if ($row["status"] != "active") {
        $toastError = "❌ Your account has been blocked by admin.";
    }
    else {
        // ✅ Check password
        if (password_verify($password, $row["password"])) {

            $_SESSION["uid"]   = $row["uid"];
            $_SESSION["uname"] = $row["uname"];
            $_SESSION["role"]  = $row["role"];
if ($row["role"] == "admin") {

    header("Location: http://localhost/php_program/project/admin/admin_dashboard.php");

} elseif ($row["role"] == "company") {

    if ($row["is_completed"] == 1) {
        header("Location: http://localhost/php_program/project/home.php");
    } else {
        header("Location: http://localhost/php_program/project/company/profile_complete.php");
    }

} else {

  if ($row["is_completed"] == 1) {
        header("Location: http://localhost/php_program/project/home.php");
    } else {
        header("Location: http://localhost/php_program/project/seeker/seeker_profile.php");
    }
}

            exit;
        } else {
            $toastError = "❌ Wrong password";
        }
    }

} else {
    $toastError = "❌ User not found";
}
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Career Craft | Login</title>

<link href="../dist/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="image/logo3.jpg" type="image/png">

<style>
@keyframes float {
  0% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
  100% { transform: translateY(0); }
}
.animate-float { animation: float 6s ease-in-out infinite; }
.req { color: #f87171; }
</style>
</head>

<body class="min-h-screen bg-black flex items-center justify-center">

<!-- SUCCESS TOAST -->
<?php if ($success): ?>
<div class="fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
✔ Login successful
</div>
<?php endif; ?>

<!-- ERROR TOAST -->
<?php if ($toastError): ?>
<div class="fixed top-5 right-5 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
<?= $toastError ?>
</div>
<?php endif; ?>

<!-- FULL SCREEN CARD -->
<div class="w-full h-screen flex items-center justify-center px-4">

<div class="w-full max-w-6xl h-[90vh] bg-[#0f0f0f] rounded-2xl shadow-2xl overflow-hidden
            flex flex-col md:flex-row border border-white/10">

<!-- LEFT : LOGIN FORM -->
<div class="md:w-1/2 flex items-center justify-center p-8 text-white">
     <a href="http://localhost/php_program/project/auth/signup.php" class="absolute left-4 top-4 text-yellow-400 text-sm hover:underline">← Back</a>

<div class="w-full max-w-md">

<h2 class="text-3xl font-bold text-[#D7AE27] mb-6">Welcome Back</h2>

<form method="POST" novalidate class="space-y-4">

<div>
<label>Email <span class="req">*</span></label>
<input id="email" name="email"
value="<?= htmlspecialchars($email) ?>"
class="w-full bg-black border border-white/20 rounded px-4 py-2">
<p id="emailErr" class="text-red-400 text-sm"><?= $emailErr ?></p>
</div>

<div>
<label>Password <span class="req">*</span></label>
<div class="relative">
<input type="password" id="password" name="password"
class="w-full bg-black border border-white/20 rounded px-4 py-2 pr-10">
<span id="passToggle" onclick="togglePassword()"
class="absolute right-3 top-2.5 cursor-pointer text-white/70">
<i class="fa-solid fa-eye-slash"></i>
</span>
</div>
<p id="passwordErr" class="text-red-400 text-sm"><?= $passwordErr ?></p>
</div>

<button class="w-full bg-[#D7AE27] text-black py-2 rounded font-bold hover:bg-yellow-500">
Login
</button>

<p class="text-center text-sm text-white/70 mt-4">
Don’t have an account?
<a href="signup.php" class="text-[#D7AE27] hover:underline font-semibold">
Sign Up
</a>
</p>

<div class="text-center mt-2">
<a href="forget.php"
class="text-white text-sm hover:underline hover:text-yellow-300">
Forgot Password?
</a>
</div>

</form>
</div>
</div>

<!-- RIGHT : DESIGN -->
<div class="md:w-1/2 bg-black relative hidden md:flex items-center justify-center overflow-hidden">

<div class="absolute w-72 h-72 bg-[#D7AE27] rounded-full -top-12 -right-12 animate-float"></div>
<div class="absolute w-48 h-48 bg-[#D7AE27] rounded-full bottom-12 right-12 animate-float"></div>
<div class="absolute w-56 h-56 bg-[#D7AE27] rounded-full top-32 left-10 animate-float"></div>

<div class="z-10 text-center text-white px-4">
<h1 class="text-5xl font-bold">Career Craft</h1>
<p class="text-white/80 mt-2">Build Your Future With Us</p>
</div>

</div>

</div>
</div>

<script>
// 👁 Toggle password
function togglePassword() {
    const pass = document.getElementById("password");
    const icon = document.querySelector("#passToggle i");
    pass.type = pass.type === "password" ? "text" : "password";
    icon.classList.toggle("fa-eye");
    icon.classList.toggle("fa-eye-slash");
}

// ✅ LIVE VALIDATION (YOUR APPROVED METHOD)
email.addEventListener("input", () => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    emailErr.textContent =
        email.value === "" ? "Email required" :
        !pattern.test(email.value) ? "Invalid email" : "";
});

password.addEventListener("input", () => {
    passwordErr.textContent =
        password.value === "" ? "Password required" :
        password.value.length < 6 ? "Minimum 6 characters" : "";
});
</script>

</body>
</html>