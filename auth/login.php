<?php
session_start();
require "../config/db.php";

$successMessage = "";
// LOGIN ATTEMPT LIMIT
$max_attempts = 5;
$lock_time = 300; // 5 minutes

if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt'] = 0;
}

$locked = false;
$remaining = 0;

// Check if locked
if ($_SESSION['attempts'] >= $max_attempts) {

    $elapsed = time() - $_SESSION['last_attempt'];

    if ($elapsed < $lock_time) {
        $locked = true;
        $remaining = $lock_time - $elapsed;
    } else {
        // Reset after lock time
        $_SESSION['attempts'] = 0;
        $_SESSION['last_attempt'] = 0;
    }
}

$email = $password = $captcha = "";
$emailErr = $passwordErr = $captchaErr = "";
$toastError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$locked) {

    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $captcha  = trim($_POST["captcha"]);

    // VALIDATION
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
    elseif ($captcha != $_SESSION["vercode"]) {
        $captchaErr = "Invalid Captcha";
        $_SESSION['attempts']++;
        $_SESSION['last_attempt'] = time();
    }

    if ($emailErr=="" && $passwordErr=="" && $captchaErr=="") {

        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email=?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($res) == 1) {

            $row = mysqli_fetch_assoc($res);

            if ($row["status"] != "active") {
                $toastError = "❌ Your account has been blocked by admin.";
            } else {

                if (password_verify($password, $row["password"])) {

                    // SUCCESS → RESET ATTEMPTS
                    $_SESSION['attempts'] = 0;
                    $_SESSION['last_attempt'] = 0;

                    $_SESSION["uid"]   = $row["uid"];
                    $_SESSION["uname"] = $row["uname"];
                    $_SESSION["role"]  = $row["role"];

                     $successMessage = "✔ Login Successfully";
                 
                    // ROLE REDIRECT
                    if ($row["role"] == "admin") {
                        header("Location: ../admin/admin_dashboard.php");

                    } elseif ($row["role"] == "company") {

                        if ($row["is_completed"] == 1)
                            header("Location: ../home.php");
                        else
                            header("Location: ../company/profile_complete.php");

                    } else {

                        if ($row["is_completed"] == 1)
                            header("Location: ../home.php");
                        else
                            header("Location: ../seeker/seeker_profile.php");
                    }
                    exit;

                } else {
                    $_SESSION['attempts']++;
                    $_SESSION['last_attempt'] = time();
                }
            }
        } else {
            $_SESSION['attempts']++;
            $_SESSION['last_attempt'] = time();
        }

        if ($_SESSION['attempts'] >= $max_attempts) {
            $locked = true;
            $remaining = $lock_time;
        }

        if (!$locked)
            $toastError = "❌ Invalid login. Remaining attempts: " . ($max_attempts - $_SESSION['attempts']);
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

<?php if ($successMessage): ?>
<p class="text-green-500 text-center font-semibold mt-3">
    <?= $successMessage ?>
</p>
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
     <a href="../auth/signup.php" class="absolute left-4 top-4 text-yellow-400 text-sm hover:underline">← Back</a>

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
<!-- CAPTCHA -->
<div>
<label>Enter Captcha <span class="req">*</span></label>

<div class="flex items-center gap-3">

<img src="captcha.php" id="captchaImage"
class="border border-white/30 rounded h-12">

<button type="button"
onclick="refreshCaptcha()"
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
// 👁 Toggle password
function togglePassword() {
    const pass = document.getElementById("password");
    const icon = document.querySelector("#passToggle i");
    pass.type = pass.type === "password" ? "text" : "password";
    icon.classList.toggle("fa-eye");
    icon.classList.toggle("fa-eye-slash");
}

// ✅ LIVE VALIDATION
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

function refreshCaptcha() {
    document.getElementById("captchaImage").src =
        "captcha.php?" + Date.now();
}

// ✅ AUTO HIDE SUCCESS TOAST (ALWAYS CHECK)
const successToast = document.getElementById("successToast");
if (successToast) {
    setTimeout(() => {
        successToast.remove();
    }, 3000);
}
</script>

<?php if ($locked): ?>
<script>
let timeLeft = <?= $remaining ?>;
const countdown = document.getElementById("countdown");
const loginBtn = document.getElementById("loginBtn");

function formatTime(seconds) {
    let m = Math.floor(seconds / 60);
    let s = seconds % 60;

    if (m < 10) m = "0" + m;
    if (s < 10) s = "0" + s;

    return m + ":" + s;
}

// Show first time
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
</script>
<?php endif; ?>
</body>
</html>