<?php
session_start();

$name = $_SESSION['name'] ?? "";
$email = $_SESSION['email'] ?? "";

$mobile = $age = $city = $address = $dob = $qualification = "";
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["mobile"]) || !preg_match("/^[0-9]{10}$/", $_POST["mobile"])) {
        $errors['mobile'] = "Valid 10-digit mobile number required";
    } else {
        $mobile = $_POST["mobile"];
    }

    if (empty($_POST["age"]) || $_POST["age"] < 18) {
        $errors['age'] = "Age must be 18 or above";
    } else {
        $age = $_POST["age"];
    }

    if (empty($_POST["city"])) {
        $errors['city'] = "City is required";
    } else {
        $city = $_POST["city"];
    }

    if (empty($_POST["address"])) {
        $errors['address'] = "Address is required";
    } else {
        $address = $_POST["address"];
    }

 if (!empty($_POST["dob"])) {
    $dobDate = new DateTime($_POST["dob"]);
    $today = new DateTime();
    $ageCalc = $today->diff($dobDate)->y;

    if ($ageCalc < 18) {
        $errors['dob'] = "You must be at least 18 years old";
    }
}
    if (empty($_POST["qualification"])) {
        $errors['qualification'] = "Qualification required";
    } else {
        $qualification = $_POST["qualification"];
    }

    if (!isset($_POST["accept"])) {
        $errors['accept'] = "Accept terms & conditions";
    }

    if (empty($errors)) {
        $success = "Registration completed successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Complete Registration</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#020617] to-[#0f172a]">

<form method="POST"
class="w-full max-w-lg bg-[#020617]/90 text-white rounded-2xl shadow-xl p-8 space-y-4">

<h2 class="text-2xl font-semibold mb-2">Complete Your Profile</h2>

<!-- Autofill Name -->
<input readonly value="<?= htmlspecialchars($name) ?>"
class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3 opacity-70">

<!-- Autofill Email -->
<input readonly value="<?= htmlspecialchars($email) ?>"
class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3 opacity-70">

<!-- Mobile -->
<input name="mobile" placeholder="Mobile Number"
class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3">
<p class="text-red-400 text-sm"><?= $errors['mobile'] ?? "" ?></p>

<!-- Age -->
<input type="number" name="age" placeholder="Age"
class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3">
<p class="text-red-400 text-sm"><?= $errors['age'] ?? "" ?></p>

<!-- City -->
<select name="city"
class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3">
<option value="">Select City</option>
<option>Ahmedabad</option>
<option>Surat</option>
<option>Vadodara</option>
<option>Rajkot</option>
</select>
<p class="text-red-400 text-sm"><?= $errors['city'] ?? "" ?></p>

<!-- Address -->
<textarea name="address" placeholder="Address"
class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3"></textarea>
<p class="text-red-400 text-sm"><?= $errors['address'] ?? "" ?></p>

<!-- DOB with editable input and calendar icon -->
<div class="relative w-full">
    <input type="text" id="dob" name="dob"
        value="<?= htmlspecialchars($dob) ?>"
        placeholder="Select Date of Birth"
        class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3 text-white pr-12 cursor-text">

    <!-- Calendar icon -->
    <div class="absolute inset-y-0 right-3 flex items-center cursor-pointer" id="dob-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
</div>
<p class="text-red-400 text-sm"><?= $errors['dob'] ?? "" ?></p>

<!-- Qualification -->
<input name="qualification" placeholder="Qualification"
class="w-full bg-[#020617] border border-gray-700 rounded-xl px-4 py-3">
<p class="text-red-400 text-sm"><?= $errors['qualification'] ?? "" ?></p>

<!-- Terms -->
<div class="flex items-center gap-2 text-sm text-gray-300">
    <input type="checkbox" name="accept" class="accent-blue-600">
    <span>
        I accept 
        <a href="terms.php" target="_blank" class="text-blue-500 hover:underline">
            Terms & Conditions
        </a>
    </span>
</div>
<p class="text-red-400 text-sm"><?= $errors['accept'] ?? "" ?></p>

<!-- Button -->
<button class="w-full bg-blue-600 hover:bg-blue-700 py-3 rounded-xl font-semibold">
Register
</button>

<?php if ($success): ?>
<p class="text-green-400 text-center mt-2"><?= $success ?></p>
<?php endif; ?>

</form>
<script>
  const dobInput = document.querySelector("#dob");
    const dobIcon = document.querySelector("#dob-icon");

    const fp = flatpickr(dobInput, {
        dateFormat: "d-m-Y",  // display format
        maxDate: "today",
        allowInput: true,     // allows typing
    });

    // Open calendar on icon click
    dobIcon.addEventListener("click", () => {
        fp.open();
    });
</script>
</body>
</html>
