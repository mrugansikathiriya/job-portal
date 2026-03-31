<?php
require "../config/db.php";

$message = strtolower(trim($_POST['message'] ?? ''));
$email   = strtolower(trim($_POST['email'] ?? ''));

// ❌ 1. email format check
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    echo "INVALID";
    exit;
}

// 🔍 2. check company exists in DB (UPDATED VALIDATION)
$stmt = $conn->prepare("SELECT status FROM users WHERE email=? AND role='company'");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    echo "INVALID"; // ❗ changed from UNKNOWN
    exit;
}

$row = $res->fetch_assoc();

// 🚫 3. blocked company
if($row['status'] == 'blocked'){
    echo "YES";
    exit;
}

// 🔢 4. report count check
$count = $conn->prepare("SELECT COUNT(*) as total FROM fraud_reports WHERE company_email=?");
$count->bind_param("s", $email);
$count->execute();
$r = $count->get_result()->fetch_assoc();

if($r['total'] >= 10){
    echo "YES";
    exit;
}

// 🔍 5. fraud keyword logic
$fraudWords = ["pay","payment","money","fee","fees","amount","charges","charge","earn 5000 daily","earn money fast","high salary no experience","easy income","no skill required","double income","aadhaar","pan card","document verification fee","deposit","registration fee","processing fee","security deposit","advance","advance payment","initial payment","bank","bank details","account","account number","ifsc","upi","upi id","transfer","online transfer","wallet","paytm","gpay","phonepe","urgent","immediately","asap","limited time","today only","hurry",
    "last chance","click link","apply now link","download app","fake","install app","click link","apply now link","download app","install app","fast process","quick joining","no interview","direct selection","direct joining","instant job","100% selection","guaranteed job","confirm job","form filling charges",];

$isFraud = false;

foreach($fraudWords as $word){
    if(strpos($message, $word) !== false){
        $isFraud = true;
        break;
    }
}

// ✅ final result
echo $isFraud ? "YES" : "NO";
?>