<?php
session_start();
require "../config/db.php";
// 🔐 LOGIN CHECK
if(!isset($_SESSION['uid'])){
    header("Location: ../auth/login.php");
    exit();
}

// 🚫 BLOCK COMPANY USERS
if($_SESSION['role'] == "company"){
    header("Location: ../home.php?msg=not_allowed");
    exit();
}
$uid = $_SESSION['uid'] ?? 0;
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
            
        <?php if(isset($_GET['msg'])): ?>
        <script>

        <?php if($_GET['msg'] == "success"): ?>
        alert("🚨 Fraud Report Submitted! Total Reports: <?= $_GET['count'] ?? 1 ?>");

        <?php elseif($_GET['msg'] == "safe"): ?>
        alert("✅ This looks safe. Report not submitted.");

        <?php elseif($_GET['msg'] == "notfound"): ?>
        alert("⚠ Company not found");

        <?php elseif($_GET['msg'] == "empty"): ?>
        alert("⚠ Please fill all fields");

        <?php else: ?>
        alert("❌ Invalid Request");

        <?php endif; ?>

        </script>
        <?php endif; ?>

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

        <div class="bg-black/50 border border-[#D7AE27]/40 p-6 rounded-2xl shadow-xl">

        <h2 class="text-xl font-semibold text-[#D7AE27] mb-4">📝 Report Fraud</h2>

        <form action="report_action.php" method="POST" class="space-y-4">

        <input type="hidden" name="uid" value="<?= $uid ?>">
        <!-- ✅ COMPANY NAME -->
        <div class="relative">

        <input type="text" id="cname" name="cname" placeholder="Select Company"
        class="w-full p-3 bg-black border border-gray-600 rounded-lg text-white"
        autocomplete="off" required>

        <!-- Dropdown -->
        <div id="suggestions"
        class="absolute w-full bg-black border border-gray-600 rounded-lg mt-1 hidden max-h-48 overflow-y-auto z-50">
        </div>

        </div>
        <!-- DETAILS -->
        <textarea name="details" placeholder="Describe the issue..."
        class="w-full p-3 bg-black border border-gray-600 rounded-lg h-28 text-white" required></textarea>

        <!-- BUTTONS -->
        <div class="flex gap-3">

        <button type="submit"
        class="bg-[#D7AE27] text-black font-semibold px-4 py-2 rounded-lg w-full">
        Submit Report
        </button>

        <button type="button" onclick="checkFraud()"
        class="border border-[#D7AE27] text-[#D7AE27] px-4 py-2 rounded-lg w-full">
        Check Fraud
        </button>

        </div>

        <p id="result" class="mt-4 font-semibold text-lg"></p>

        </form>
        </div>
        </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>

    // ✅ FLAG VARIABLES
    let fraudChecked = false;
    let isFraud = false;

    // 🔍 LIVE COMPANY SEARCH (same as before)
    $("#cname").keyup(function(){

        let keyword = $(this).val();

        if(keyword.length < 1){
            $("#suggestions").hide();
            return;
        }

        $.post("search_company.php", {keyword: keyword}, function(data){
            $("#suggestions").html(data).show();
        });

    });

    // CLICK SELECT
    $(document).on("click", ".suggest-item", function(){
        $("#cname").val($(this).text());
        $("#suggestions").hide();
    });


    // 🤖 CHECK FRAUD
    function checkFraud() {

        let msg = document.querySelector("textarea[name='details']").value.trim();
        let company = document.querySelector("#cname").value.trim();
        let resultBox = document.getElementById("result");

        resultBox.innerHTML = "<span style='color:#D7AE27'>Checking...</span>";

        $.post("ai_check.php", {message: msg, company: company}, function(res){

            fraudChecked = true; // ✅ user checked
            isFraud = false;     // reset

            res = res.trim();

            if(res === "YES"){
                isFraud = true;
                resultBox.innerHTML = "<span style='color:red'>⚠ Fraud Detected</span>";
            }
            else if(res === "NO"){
                resultBox.innerHTML = "<span style='color:lightgreen'>✅ Safe</span>";
            }
            else if(res === "UNKNOWN"){
                resultBox.innerHTML = "<span style='color:orange'>⚠ Company not found</span>";
            }
            else{
                resultBox.innerHTML = "<span style='color:gray'>Error: " + res + "</span>";
            }
        });
    }


    // 🚫 PREVENT DIRECT SUBMIT
    document.querySelector("form").addEventListener("submit", function(e){

        let resultBox = document.getElementById("result");

        // ❌ Not checked
        if(!fraudChecked){
            e.preventDefault();
            resultBox.innerHTML = "<span style='color:red'>❗ First check fraud before submitting</span>";
            return;
        }

        // ❌ Checked but SAFE
        if(!isFraud){
            e.preventDefault();
            resultBox.innerHTML = "<span style='color:red'>❗ Only fraud cases can be reported</span>";
            return;
        }

        // ✅ If fraud → allow submit
    });

    </script>


    </body>
</html>
