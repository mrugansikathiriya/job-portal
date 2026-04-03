<?php
require "../config/db.php";

// ✅ check required fields
if(!isset($_POST['company_email'], $_POST['details'])){
    die("Invalid Request");
}

// ✅ get values
$email   = trim($_POST['company_email']);
$details = trim($_POST['details']);

// ✅ validation
if($email == "" || $details == ""){
    die("All fields required");
}

// ✅ NEW: check company email exists in users table
$check = $conn->prepare("SELECT uid FROM users WHERE email=? AND role='company'");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if($result->num_rows == 0){
    die("<script>
        alert('❌ Invalid Company Email! Not registered.');
        window.location='fraud_alert.php';
    </script>");
}

// ✅ INSERT (same as before)
$stmt = $conn->prepare("INSERT INTO fraud_reports(company_email, details) VALUES (?,?)");
$stmt->bind_param("ss", $email, $details);

if($stmt->execute()){

    // 🔢 COUNT reports
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM fraud_reports WHERE company_email=?");
    $count_stmt->bind_param("s", $email);
    $count_stmt->execute();
    $res = $count_stmt->get_result();
    $row = $res->fetch_assoc();

    $total = $row['total'];

    // 🚫 BLOCK company if >= 3 reports
    if($total >= 10){
        $block = $conn->prepare("UPDATE users SET status='blocked' WHERE email=?");
        $block->bind_param("s", $email);
        $block->execute();
    }

    echo "<script>
        alert('Report Submitted (Total Reports: $total)');
        window.location='fraud_alert.php';
    </script>";

} else {
    echo "Error: " . $conn->error;
}
?>