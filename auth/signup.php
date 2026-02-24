<?php
require "../config/db.php";
$uname = $email = $password = $contact = $role = "";
$unameErr = $emailErr = $passwordErr = $contactErr = $roleErr = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $uname = trim($_POST["uname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $contact = trim($_POST["contact"]);
    $role= $_POST["role"] ?? "";

    if ($uname == "") $unameErr = "Username required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $emailErr = "Invalid email";
    if (strlen($password) < 6) $passwordErr = "Minimum 6 characters";
    if ($contact == "") $contactErr = "Contact required";
    if ($role == "") $roleErr = "Role required";

    if ($unameErr=="" && $emailErr=="" && $passwordErr=="" && $contactErr=="" && $roleErr=="") {

        // check email exists
        $check = mysqli_query($conn, "SELECT uid FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $emailErr = "Email already exists";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query($conn,
                "INSERT INTO users (uname,email,password,role,contact)
                 VALUES ('$uname','$email','$hashed','".strtolower($role)."','$contact')"
            );

            $uid = mysqli_insert_id($conn);

            // insert role specific data
           
            if ($role == "company") {
                mysqli_query($conn, "INSERT INTO company (uid,cname) VALUES ($uid,'$uname')");
            }
            else {
                mysqli_query($conn,
                    "INSERT INTO job_seeker (uid,sname) VALUES ($uid,'$uname')"
                );
            }

            $success = true;
            $uname = $email = $password = $contact = $role = "";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Career Craft | Sign Up</title>

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

<body class="min-h-screen bg-black flex items-center justify-center px-4 overflow-x-hidden mt-10 mb-10"><?php if ($success): ?>
<div id="successToast"
     class="fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg transition-opacity duration-500">
  ✔ Registration successful
</div>
<?php endif; ?>

<div class="w-full max-w-6xl bg-[#0f0f0f] rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row border border-white/10">

<!-- LEFT -->
 <a href="http://localhost/php_program/project/home.php" class="absolute left-4 top-4 text-yellow-400 text-sm hover:underline">← Back</a>

<div class="md:w-1/2 bg-black relative flex items-center justify-center overflow-hidden">
  <div class="hidden md:block absolute w-72 h-72 bg-[#D7AE27] rounded-full -top-12 -left-12 animate-float"></div>
  <div class="hidden md:block absolute w-48 h-48 bg-[#D7AE27] rounded-full bottom-12 left-12 animate-float"></div>
  <div class="hidden md:block absolute w-56 h-56 bg-[#D7AE27] rounded-full top-32 right-10 animate-float"></div>

  <div class="z-10 text-center text-white">
    <h1 class="text-5xl font-bold">Career Craft</h1>
    <p class="text-white/80 mt-2">Shape Your Career Path</p>
  </div>
</div>

<!-- FORM -->
<div class="md:w-1/2 p-8 text-white">
<h2 class="text-2xl font-bold text-[#D7AE27] mb-6">Create Account</h2>

<form method="POST" class="space-y-4" novalidate>

<div>
<label>Username <span id="s-uname" class="req">*</span></label>
<input id="uname" name="uname" value="<?= htmlspecialchars($uname) ?>"
class="w-full bg-black border border-white/20 rounded px-4 py-2">
<p id="unameErr" class="text-red-400 text-sm"><?= $unameErr ?></p>
</div>

<div>
<label>Email <span id="s-email" class="req">*</span></label>
<input id="email" name="email" value="<?= htmlspecialchars($email) ?>"
class="w-full bg-black border border-white/20 rounded px-4 py-2">
<p id="emailErr" class="text-red-400 text-sm"><?= $emailErr ?></p>
</div>

<div class="relative">
  <label class="block mb-1">
    Password <span id="s-password" class="req">*</span>
  </label>

  <div class="relative">
    <input type="password"
           name="password"
           id="password"
           class="w-full bg-black border border-white/20 rounded px-4 py-2 pr-10">

    <!-- Eye toggle -->
    <span id="passToggle"
          onclick="togglePassword()"
          class="absolute right-3 top-2.5 cursor-pointer text-white/70">
      <i class="fa-solid fa-eye-slash"></i>
    </span>
  </div>

  <!-- ✅ FIXED ID -->
  <p id="passwordErr" class="text-red-400 text-sm">
    <?= $passwordErr ?>
  </p>
</div>
  

<div>
<label>Role <span id="s-role" class="req">*</span></label>
<div class="flex gap-6 mt-2 mb-2">
<?php foreach (["company","jobseeker"] as $r): ?>
<label>
<input type="radio" name="role" value="<?= $r ?>" <?= $role===$r?"checked":"" ?>
class="accent-[#D7AE27]">
<?= ucfirst($r) ?>
</label>
<?php endforeach; ?>
<p id="roleErr" class="text-red-400 text-sm"><?= $roleErr ?></p>
</div>

<div>
<label>Contact <span id="s-contact" class="req">*</span></label>
<input id="contact" name="contact" maxlength="10"
value="<?= htmlspecialchars($contact) ?>"
class="w-full bg-black border border-white/20 rounded px-4 py-2 mt-2">
<p id="contactErr" class="text-red-400 text-sm"><?= $contactErr ?></p>
</div>

<button class="w-full bg-[#D7AE27] text-black py-2 rounded font-bold"   onclick="location.href='http://localhost/php_program/project/auth/login.php'">
Sign Up
</button>

<p class="text-center text-sm text-white/70 mt-4">
    Already have an account?
    <a href="login.php"
       class="text-[#D7AE27] hover:underline font-semibold">
       Login
    </a>
</p>


</form>
</div>
</div>


<!-- LIVE VALIDATION -->
<script>
uname.oninput = () =>
  unameErr.textContent = uname.value.length >= 3 ? "" : "Minimum 3 characters required";

email.oninput = () =>
  emailErr.textContent = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.value) ? "" : "Invalid email format";

password.oninput = () =>
  passwordErr.textContent = password.value.length >= 6 ? "" : "Minimum 6 characters required";

contact.oninput = () =>
  contactErr.textContent = /^[0-9]{10}$/.test(contact.value) ? "" : "Contact must be exactly 10 digits";

document.querySelectorAll('[name="role"]').forEach(r =>
  r.onchange = () => roleErr.textContent = ""
);

setTimeout(() => {
    const toast = document.getElementById("successToast");
    toast.style.opacity = "0";

    setTimeout(() => toast.remove(), 500); // remove after fade-out
  }, 3000); // 3 seconds


function togglePassword() {
    const pass = document.getElementById("password");
    const icon = document.querySelector("#passToggle i");

    if (pass.type === "password") {
        pass.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye"); // open eye
    } else {
        pass.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash"); // closed eye
    }

}

</script>
    <?php include("../include/footer.php");?>

</body>

</html>