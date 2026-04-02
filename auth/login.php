<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../config/db.php";
require "../authc/csrf.php";
/* =========================
   REMEMBER ME AUTO LOGIN
========================= */
if (!isset($_SESSION['uid']) && isset($_COOKIE['remember_token'])) {

    $token = mysqli_real_escape_string($conn, $_COOKIE['remember_token']);

    $auto = mysqli_query($conn, 
        "SELECT uid, uname, role, is_completed 
            FROM users 
            WHERE remember_token='$token' AND status='active'");

    if (mysqli_num_rows($auto) == 1) {

        $user = mysqli_fetch_assoc($auto);

        session_regenerate_id(true);

        $_SESSION['uid'] = $user['uid'];
        $_SESSION['uname'] = $user['uname'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['is_completed'] = $user['is_completed'];
         if ($user["role"] == "admin") {
                        header("Location: ../admin/admin_dashboard.php");
                    } 
            elseif ($user["role"] == "company") {

                        if ($user["is_completed"] == 1)
                            header("Location: ../company/cdashboard.php");
                        else
                            header("Location: ../company/profile_complete.php");
                    } 
                    else {

                        if ($user["is_completed"] == 1)
                            header("Location: ../seeker/sdashboard.php");
                        else
                            header("Location: ../seeker/seeker_profile.php");
                    }
                   exit;

    }
}

$toastError = "";
$locked = false;
$remaining = 0;

$email = $password = $captcha = "";
$emailErr = $passwordErr = $captchaErr = "";

/* =========================
   LOGIN PROCESS
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $email    = mysqli_real_escape_string($conn, trim($_POST["email"]));
    $password = $_POST["password"];
    $captcha  = trim($_POST["captcha"]);

    if ($email == "")
        $emailErr = "Email required";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $emailErr = "Invalid email";

    if ($password == "")
        $passwordErr = "Password required";
    elseif (strlen($password) < 6)
        $passwordErr = "Minimum 6 characters";

    if ($captcha == "")
        $captchaErr = "Captcha required";
    elseif ($captcha != $_SESSION["vercode"])
        $captchaErr = "Invalid Captcha";

    if ($emailErr=="" && $passwordErr=="" && $captchaErr=="") {

        $check = mysqli_query($conn, 
            "SELECT * FROM users WHERE email='$email'");

        if (mysqli_num_rows($check) == 1) {

            $row = mysqli_fetch_assoc($check);

            /* DATABASE LOCK CHECK */
            if ($row['failed_attempts'] >= 5) {

                $elapsed = time() - $row['last_failed_attempt'];

                if ($elapsed < 300) {
                    $locked = true;
                    $remaining = 300 - $elapsed;
                    $toastError = "🚫 Account locked. Try again later.";
                } else {
                    mysqli_query($conn,
                        "UPDATE users 
                         SET failed_attempts=0, last_failed_attempt=0 
                         WHERE uid='{$row['uid']}'");
                }
            }

            if (!$locked) {

                if ($row["status"] != "active") {
                    $toastError = "❌ Account blocked by admin.";
                }

                elseif (password_verify($password, $row["password"])) {

                    /* RESET FAILED ATTEMPTS */
                    mysqli_query($conn,
                        "UPDATE users 
                         SET failed_attempts=0, last_failed_attempt=0 
                         WHERE uid='{$row['uid']}'");

                    session_regenerate_id(true);

                    $_SESSION["uid"]   = $row["uid"];
                    $_SESSION["uname"] = $row["uname"];
                    $_SESSION["role"]  = $row["role"];
                    $_SESSION["is_completed"] = $row["is_completed"];
                    $_SESSION['login_success'] = "Login successful! Welcome back.";

                    /* REMEMBER ME */
                    if (isset($_POST['remember'])) {

                        $token = bin2hex(random_bytes(32));

                        setcookie("remember_token", $token, 
                                  time() + (86400*30), "/", "", false, true);

                        mysqli_query($conn,
                            "UPDATE users 
                             SET remember_token='$token' 
                             WHERE uid='{$row['uid']}'");
                    }

                    /* ROLE REDIRECT */
                    if ($row["role"] == "admin") {
                        header("Location: ../admin/admin_dashboard.php");
                    } 
                    elseif ($row["role"] == "company") {

                        if ($row["is_completed"] == 1)
                            header("Location: ../company/cdashboard.php");
                        else
                            header("Location: ../company/profile_complete.php");
                    } 
                    else {

                        if ($row["is_completed"] == 1)
                            header("Location: ../seeker/sdashboard.php");
                        else
                            header("Location: ../seeker/seeker_profile.php");
                    }
                    exit;

                } else {

                    $now = time();

                    mysqli_query($conn,
                        "UPDATE users 
                         SET failed_attempts = failed_attempts + 1,
                             last_failed_attempt = '$now'
                         WHERE uid='{$row['uid']}'");

                    $toastError = "❌ Invalid login credentials.";
                }
            }

        } else {
            $toastError = "❌ Invalid login credentials.";
        }
    }
        regenerateCSRFToken(); 

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
<link rel="icon" href="../image/logo3.jpg" type="image/png">

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
<?php if(isset($_GET['registered'])): ?>
<div id="successToast"
     class="fixed top-24 right-5 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 
            flex items-center justify-between gap-4 min-w-[300px]
            transition-opacity duration-500">

    <span>✔ Registration successful. Please login.</span>

    <button onclick="closeToast('successToast')" 
            class="text-xl font-bold hover:text-gray-200 leading-none">
        &times;
    </button>
</div>
<?php endif; ?>


<!-- ERROR TOAST -->
<?php if ($toastError): ?>
<div id="errorToast"
     class="fixed top-24 right-5 bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 
            flex items-center justify-between gap-4 min-w-[300px]
            transition-opacity duration-500">

    <span><?= $toastError ?></span>

    <button onclick="closeToast('errorToast')" 
            class="text-xl font-bold hover:text-gray-200 leading-none">
        &times;
    </button>
</div>
<?php endif; ?>

<!-- FULL SCREEN CARD -->
<div class="w-full h-screen flex items-center justify-center px-4">

<div class="w-full max-w-6xl h-[90vh] bg-[#0f0f0f] rounded-2xl shadow-2xl overflow-hidden
            flex flex-col md:flex-row border border-white/10">

<!-- LEFT : LOGIN FORM -->
<div class="md:w-1/2 flex items-center justify-center p-8 text-white">
     <a href="../auth/signup.php" class="absolute left-4 top-4 text-yellow-400 text-sm hover:underline">← Back</a>

<div class="w-full max-w-md">

<h2 class="text-3xl font-bold text-[#D7AE27] mb-6">Welcome Back</h2>

<form method="POST" novalidate class="space-y-4">
<input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
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
<!-- CAPTCHA -->
<div>
<label>Enter Captcha <span class="req">*</span></label>

<div class="flex items-center gap-3">

<img src="captcha.php" id="captchaImage"
class="border border-white/30 rounded h-12">
<button type="button" id="refreshBtn"
class="text-yellow-400 text-xl hover:rotate-180 transition duration-300">
<i class="fa-solid fa-rotate-right"></i>
</button>

</div>

<input type="text" name="captcha"
class="w-full bg-black border border-white/20 rounded px-4 py-2 mt-2">

<p class="text-red-400 text-sm"><?= $captchaErr ?></p>
</div>
<?php if ($locked): ?>
<div id="lockMessage"
class="fixed top-5 right-5 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
🚫 Too many failed attempts.
Try again in <span id="countdown"><?= $remaining ?></span>
</div>
<?php endif; ?>
<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="remember">
    Remember Me
</label>

<button id="loginBtn"
class="w-full bg-[#D7AE27] text-black py-2 rounded font-bold hover:bg-yellow-500 disabled:opacity-50"
<?= $locked ? 'disabled' : '' ?>>
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
document.addEventListener("DOMContentLoaded", function () {

    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const emailErr = document.getElementById("emailErr");
    const passwordErr = document.getElementById("passwordErr");

    // 👁 Toggle password
    window.togglePassword = function () {
        const pass = document.getElementById("password");
        const icon = document.querySelector("#passToggle i");
        pass.type = pass.type === "password" ? "text" : "password";
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
    };

    // ✅ LIVE VALIDATION
    if (email) {
        email.addEventListener("input", function () {
            const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

            if (email.value === "") {
                emailErr.textContent = "Email required";
            } else if (!pattern.test(email.value)) {
                emailErr.textContent = "Invalid email";
            } else {
                emailErr.textContent = "";
            }
        });
    }

    if (password) {
        password.addEventListener("input", function () {
            if (password.value === "") {
                passwordErr.textContent = "Password required";
            } else if (password.value.length < 6) {
                passwordErr.textContent = "Minimum 6 characters";
            } else {
                passwordErr.textContent = "";
            }
        });
    }

        const refreshBtn = document.getElementById("refreshBtn");

    if (refreshBtn) {
        refreshBtn.addEventListener("click", function () {
            const captchaImg = document.getElementById("captchaImage");
            captchaImg.src = "captcha.php?rand=" + Date.now();
        });
    }
});


function closeToast(id) {
    const toast = document.getElementById(id);
    if (toast) {
        toast.style.opacity = "0";
        setTimeout(() => toast.remove(), 500);
    }
}

// Auto hide success after 60 seconds
const successToast = document.getElementById("successToast");
if (successToast) {
    setTimeout(() => closeToast("successToast"), 60000);
}

// Auto hide error after 60 seconds
const errorToast = document.getElementById("errorToast");
if (errorToast) {
    setTimeout(() => closeToast("errorToast"), 60000);
}
</script>

<?php if ($locked): ?>
<script>
document.addEventListener("DOMContentLoaded", function(){

    let timeLeft = <?= $remaining ?>;
    const countdown = document.getElementById("countdown");
    const loginBtn = document.getElementById("loginBtn");

    function formatTime(seconds) {
        let m = Math.floor(seconds / 60);
        let s = seconds % 60;
        return String(m).padStart(2,'0') + ":" + String(s).padStart(2,'0');
    }

    countdown.textContent = formatTime(timeLeft);

    const timer = setInterval(() => {
        timeLeft--;
        countdown.textContent = formatTime(timeLeft);

        if (timeLeft <= 0) {
            clearInterval(timer);
            loginBtn.disabled = false;
            document.getElementById("lockMessage").remove();
        }
    }, 1000);

});
</script>
<?php endif; ?>
</body>
</html>