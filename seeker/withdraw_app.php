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
    SELECT a.aid 
    FROM application a
    JOIN job_seeker js ON a.sid = js.sid
    WHERE a.aid='$aid' AND js.uid='$uid' AND a.status='pending'
");
if (mysqli_num_rows($check) == 0) {
    die("Unauthorized or already processed");
}

/* ================= UPDATE STATUS ================= */

if(mysqli_query($conn, "UPDATE application SET status='withdrawn' WHERE aid='$aid'")){

    // 🔥 STEP 1: Get job + company info
    $query = mysqli_query($conn, "
        SELECT j.cid, j.title, js.sname
        FROM application a
        JOIN job j ON a.jid = j.jid
        JOIN job_seeker js ON a.sid = js.sid
        WHERE a.aid = '$aid'
    ");

if($data = mysqli_fetch_assoc($query)){

    $cid = $data['cid'];
    $jobTitle = $data['title'];
    $seekerName = $data['sname'];

    $company = mysqli_query($conn, "SELECT uid FROM company WHERE cid='$cid'");

    if($comp = mysqli_fetch_assoc($company)){
        $company_uid = $comp['uid'];

        $message = mysqli_real_escape_string($conn, 
            "$seekerName has withdrawn application for $jobTitle"
        );

        mysqli_query($conn, "
            INSERT INTO notifications (uid, message, is_read, created_at)
            VALUES ('$company_uid', '$message', 0, NOW())
        ");
    }
}

     regenerateCSRFToken(); // 🔥 important
    header("Location: all_application.php?msg=withdrawn");
    exit();
}


 else {
    echo "Something went wrong!";
}
?>