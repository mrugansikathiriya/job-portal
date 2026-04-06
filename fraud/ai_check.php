<?php
require "../config/db.php";

$company = $_POST['company'] ?? '';
$message = strtolower($_POST['message'] ?? '');

if(empty($company)){
    echo "UNKNOWN";
    exit();
}

// ✅ CHECK COMPANY EXIST
$res = mysqli_query($conn, "SELECT * FROM company WHERE cname='$company'");

if(mysqli_num_rows($res) == 0){
    echo "UNKNOWN";
    exit();
}

// 🔢 COUNT FRAUD REPORTS
$countRes = mysqli_query($conn, "SELECT COUNT(*) as total FROM fraud_reports WHERE cname='$company'");
$countRow = mysqli_fetch_assoc($countRes);
$totalReports = $countRow['total'];

// 🚫 AUTO BLOCK IF REPORTS >= 10
if($totalReports >= 20){

    mysqli_query($conn, "UPDATE users SET status='blocked' WHERE uname='$company'");

    echo "BLOCKED";
    exit();
}

// 🤖 FRAUD KEYWORDS
$fraud_keywords = [
    "pay", "money", "fee", "registration", "urgent", "bank",
    "account number", "ifsc", "upi", "otp", "credit card",
    "debit card", "pin", "cvv",
    "no interview", "direct job", "guaranteed job", "100% job",
    "instant joining", "no experience required",
    "send aadhar", "send pan", "upload documents", "verify kyc",
    "document verification fee", "hr asking money",
    "consultancy fee", "placement charges",
    "fake job offers without interview",
    "asking money for registration/training",
    "unrealistic salary offers",
    "urgent hiring pressure",
    "request for bank details",
    "no company website",
    "suspicious links"
];

// 🔍 CHECK MESSAGE
foreach($fraud_keywords as $word){
    if(strpos($message, $word) !== false){
        echo "YES";
        exit();
    }
}

echo "NO";
?>