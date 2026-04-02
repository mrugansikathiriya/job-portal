<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

// 🔐 Only seeker allowed
if (!isset($_SESSION['uid']) || $_SESSION['role'] != 'seeker') {
    header("Location: ../auth/login.php");
    exit();
}

// ✅ CSRF check (UPDATED FUNCTION NAME)
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    die("Invalid CSRF Token");
}

$aid = mysqli_real_escape_string($conn, $_POST['aid']);
$uid = mysqli_real_escape_string($conn, $_SESSION['uid']);

/* ================= CHECK OWNERSHIP ================= */
$check = mysqli_query($conn, "
    SELECT aid FROM application 
    WHERE aid='$aid' AND uid='$uid' AND status='pending'
");

if (mysqli_num_rows($check) == 0) {
    die("Unauthorized or already processed");
}

/* ================= UPDATE STATUS ================= */
$update = mysqli_query($conn, "
    UPDATE application 
    SET status='withdrawn' 
    WHERE aid='$aid'
");

if ($update) {
    regenerateCSRFToken(); // 🔥 important
    header("Location: all_application.php?msg=withdrawn");
    exit();
} else {
    echo "Something went wrong!";
}
?>