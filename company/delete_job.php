<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";   // ✅ ADD THIS
require "../auth/session_check.php";

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    header("Location: ../auth/login.php");
    exit();
}

// ✅ Only allow POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method");
}

  if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }
$uid = $_SESSION['uid'];
$jid = intval($_POST['jid']);   // ✅ Changed from GET to POST

// Check ownership
$jobRes = mysqli_query($conn, 
    "SELECT job.jid 
     FROM job 
     JOIN company ON job.cid = company.cid 
     WHERE job.jid='$jid' AND company.uid='$uid'"
);

if(mysqli_num_rows($jobRes) == 0){
    die("Unauthorized or job not found");
}
/* ================= GET JOB + COMPANY NAME ================= */
$jobDataRes = mysqli_query($conn, "
    SELECT job.title, company.cname 
    FROM job 
    JOIN company ON job.cid = company.cid
    WHERE job.jid='$jid'
");

$jobData = mysqli_fetch_assoc($jobDataRes);

$job_title = $jobData['title'];
$company_name = $jobData['cname'];

/* ================= INSERT NOTIFICATION ================= */
$seekers = mysqli_query($conn, "
    SELECT uid FROM users WHERE role='seeker'
");

$message = $company_name . " company deleted job for " . $job_title;

while($row = mysqli_fetch_assoc($seekers)){
    $seeker_uid = $row['uid'];

    mysqli_query($conn, "
        INSERT INTO notifications (uid, message, is_read)
        VALUES ('$seeker_uid', '$message', 0)
    ");
}
// Delete job
mysqli_query($conn, "DELETE FROM job WHERE jid='$jid'");

// Regenerate token (best practice)
            regenerateCSRFToken();

header("Location: view_job.php");
exit();