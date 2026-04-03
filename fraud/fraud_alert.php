<?php
session_start();
require "../config/db.php";

$user_id = $_SESSION['uid'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Fraud Alert</title>
<link href="../dist/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="../image/logo3.jpg" type="image/png"></head></head>

<body class="bg-black text-white">
<a href="http://localhost/php_program/project/home.php"
class="inline-block mb-5 mt-2 ml-5 text-yellow-400 text-sm hover:underline ">
← Back
</a>
<div class="max-w-5xl mx-auto p-6">

<!-- 🔴 Header -->
<h1 class="text-4xl font-bold text-[#D7AE27] mb-6 text-center tracking-wide">
⚠ Fraud Alert
</h1>

<!-- 🚫 Common Scams -->
<div class="bg-black/50 backdrop-blur-md border border-[#D7AE27]/30 p-5 rounded-2xl shadow-lg mb-4">
<h2 class="text-xl font-semibold text-[#D7AE27] mb-3">🚫 Common Job Scams</h2>
<ul class="list-disc ml-5 text-gray-300 space-y-1">
<li>Fake job offers without interview</li>
<li>Asking money for registration/training</li>
<li>Unrealistic salary offers</li>
</ul>
</div>

<!-- ⚠ Warning Signs -->
<div class="bg-black/50 backdrop-blur-md border border-[#D7AE27]/30 p-5 rounded-2xl shadow-lg mb-4">
<h2 class="text-xl font-semibold text-[#D7AE27] mb-3">⚠ Warning Signs</h2>
<ul class="list-disc ml-5 text-gray-300 space-y-1">
<li>Urgent hiring pressure</li>
<li>Request for bank details</li>
<li>No company website</li>
<li>Suspicious links</li>
</ul>
</div>

<!-- 🔐 Safety Tips -->
<div class="bg-black/50 backdrop-blur-md border border-[#D7AE27]/30 p-5 rounded-2xl shadow-lg mb-4">
<h2 class="text-xl font-semibold text-[#D7AE27] mb-3">🔐 Safety Tips</h2>
<ul class="list-disc ml-5 text-gray-300 space-y-1">
<li>Verify company website</li>
<li>Never pay money</li>
<li>Use official email only</li>
</ul>
</div>

<!-- 📝 Report Form -->
<div class="bg-black/50 backdrop-blur-md border border-[#D7AE27]/40 p-6 rounded-2xl shadow-xl">
<h2 class="text-xl font-semibold text-[#D7AE27] mb-4">📝 Report Fraud</h2>

<form action="report_action.php" method="POST" class="space-y-4">

<input type="hidden" name="user_id" value="<?= $user_id ?>">

<input type="email" name="company_email" placeholder="Company Email"
class="w-full p-3 bg-black/70 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-[#D7AE27]" required>

<textarea name="details" placeholder="Describe the issue..."
class="w-full p-3 bg-black/70 border border-gray-600 rounded-lg h-28 text-white focus:outline-none focus:border-[#D7AE27]" required></textarea>

<!-- 🔘 Buttons -->
<div class="flex gap-3">
<button type="submit"
class="bg-[#D7AE27] text-black font-semibold px-4 py-2 rounded-lg hover:bg-yellow-400 transition w-full">
Submit Report
</button>

<button type="button" onclick="checkFraud()"
class="bg-black border border-[#D7AE27] text-[#D7AE27] px-4 py-2 rounded-lg hover:bg-[#D7AE27] hover:text-black transition w-full">
Check Fraud
</button>
</div>

<!-- 🔍 Result -->
<p id="result" class="mt-4 font-semibold text-lg"></p>

</form>

</div>

</div>

<!-- ✅ jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function checkFraud() {

    let msg = document.querySelector("textarea[name='details']").value.trim();
    let email = document.querySelector("input[name='company_email']").value.trim();
    let resultBox = document.getElementById("result");

    resultBox.innerHTML = "<span style='color:#D7AE27'>Checking...</span>";

    $.post("ai_check.php", {message: msg, email: email}, function(res){

        res = res.trim();

        if(res === "YES"){
            resultBox.innerHTML = "<span style='color:red'>⚠ Fraud Detected</span>";
        }
        else if(res === "NO"){
            resultBox.innerHTML = "<span style='color:lightgreen'>✅ Safe</span>";
        }
        else if(res === "UNKNOWN"){
            resultBox.innerHTML = "<span style='color:orange'>⚠ Company not found</span>";
        }
        else if(res === "INVALID"){
            resultBox.innerHTML = "<span style='color:red'>❌ Invalid Email</span>";
        }
        else{
            resultBox.innerHTML = "<span style='color:gray'>⚠ Error: " + res + "</span>";
        }
    });
}
</script>

</body>
</html>