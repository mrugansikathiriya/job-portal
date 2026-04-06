<?php
session_start();
require "../config/db.php";

if($_SERVER['REQUEST_METHOD'] !== "POST"){
    header("Location: fraud_alert.php?msg=invalid");
    exit();
}

$user_id = $_POST['user_id'] ?? 0;
$cname = trim($_POST['cname'] ?? '');
$details = strtolower(trim($_POST['details'] ?? ''));

if(empty($cname) || empty($details)){
    header("Location: fraud_alert.php?msg=empty");
    exit();
}

// ✅ CHECK COMPANY EXISTS
$res = mysqli_query($conn, "SELECT * FROM company WHERE cname='$cname'");
if(mysqli_num_rows($res) == 0){
    header("Location: fraud_alert.php?msg=notfound");
    exit();
}

// 🔥 SAME FRAUD LOGIC (AUTO CHECK)
$fraud_keywords = [
    "pay","money","fee","registration","urgent","bank",
    "account number","ifsc","upi","otp","credit card","debit card","pin","cvv",
    "no interview","direct job","guaranteed job","100% job",
    "instant joining","no experience required",
    "send aadhar","send pan","upload documents","verify kyc",
    "document verification fee","hr asking money","consultancy fee","placement charges"
];

$isFraud = false;

foreach($fraud_keywords as $word){
    if(strpos($details, $word) !== false){
        $isFraud = true;
        break;
    }
}

// ❌ IF SAFE → DO NOT INSERT
if(!$isFraud){
    header("Location: fraud_alert.php?msg=safe");
    exit();
}

// ✅ INSERT REPORT
mysqli_query($conn, "INSERT INTO fraud_reports (user_id, cname, details)
VALUES ('$user_id', '$cname', '$details')");

// ✅ COUNT REPORTS
$countRes = mysqli_query($conn, "SELECT COUNT(*) as total FROM fraud_reports WHERE cname='$cname'");
$countRow = mysqli_fetch_assoc($countRes);
$total = $countRow['total'];

// ✅ SUCCESS
header("Location: fraud_alert.php?msg=success&count=$total");
exit();
?>