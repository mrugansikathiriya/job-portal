<?php
require "../config/db.php";
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}
$email = $_POST['email'] ?? '';

if($email != ""){

    $stmt = $conn->prepare("UPDATE users SET status='blocked' WHERE email=?");
    $stmt->bind_param("s", $email);

    if($stmt->execute()){
        header("Location: fraud_reports.php");
        exit;
    } else {
        echo "Error blocking company";
    }
}
?>