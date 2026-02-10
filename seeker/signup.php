<?php
// ---------- PHP VALIDATION ----------
$name = $email = $password = "";
$nameErr = $emailErr = $passwordErr = $agreeErr = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // First Name
    if (empty($_POST["name"])) {
        $nameErr = "First name is required";
    } else {
        $name = trim($_POST["name"]);
        if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
            $nameErr = "Only letters allowed";
        }
    }

    // Email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } elseif (strlen($_POST["password"]) < 6) {
        $passwordErr = "Minimum 6 characters required";
    } else {
        $password = $_POST["password"];
    }

    // Agreement
    if (!isset($_POST["agree"])) {
        $agreeErr = "You must agree to continue";
    }

    // Success
    if ($nameErr == "" && $emailErr == "" && $passwordErr == "" && $agreeErr == "") {
        header("Location: s2.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
 <title>CareerCraft | Sign up</title>
     <link href="../dist/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#020617] to-[#0f172a]">

<form method="POST" class="w-full max-w-md bg-[#020617]/90 text-white rounded-2xl shadow-xl p-8 space-y-5">

    <h2 class="text-2xl text-center font-semibold">Sign up</h2>

    <!-- Google Button -->
    <button type="button"
        class="w-full flex items-center justify-center gap-3 border border-gray-700 rounded-xl py-3 hover:bg-gray-800 transition">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5">
        Continue with Google
    </button>

    <div class="text-center text-gray-400">OR</div>

    <!-- Name -->
    <div>
        <input type="text" name="name" placeholder="First Name"
            value="<?= htmlspecialchars($name) ?>"
            class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p class="text-red-400 text-sm"><?= $nameErr ?></p>
    </div>

    <!-- Email -->
    <div>
        <input type="text" name="email" placeholder="Email"
            value="<?= htmlspecialchars($email) ?>"
            class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p class="text-red-400 text-sm"><?= $emailErr ?></p>
    </div>

    <!-- Password -->
    <div class="relative">
        <input type="password" name="password" id="password" placeholder="Password"
            class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
           <span id="passToggle" onclick="togglePassword()"
          class="absolute right-4 top-3 cursor-pointer text-gray-400 text-xl">
        <i class="fa-solid fa-eye-slash"></i>
    </span>

        <p class="text-red-400 text-sm"><?= $passwordErr ?></p>
    </div>

    <!-- Agreement -->
    <div class="flex items-center gap-2 text-sm text-gray-300">
        <input type="checkbox" name="agree" class="accent-blue-600">
        <span>I agree to the <a href="terms.php" target="_blank" class="text-blue-500 hover:underline">
            Terms & Conditions
        </a></span>
    </div>
    <p class="text-red-400 text-sm"><?= $agreeErr ?></p>

    <!-- Button -->
    <button type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 py-3 rounded-xl font-semibold transition">
        Join
    </button>

    <!-- Success -->
    <?php if ($success): ?>
        <p class="text-green-400 text-center"><?= $success ?></p>
    <?php endif; ?>

</form>

<script>
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

</body>
</html>
