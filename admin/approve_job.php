<?php
require "admin_auth.php";       
require "../config/db.php";
require "../authc/csrf.php";

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    if (isset($_POST['jid']) && is_numeric($_POST['jid'])) {

        $jid = intval($_POST['jid']);

        // ================= UPDATE JOB STATUS =================
        $update = mysqli_query($conn, "
            UPDATE job 
            SET is_approve='approved' 
            WHERE jid=$jid
        ") or die(mysqli_error($conn));

        // ================= FETCH COMPANY UID, COMPANY NAME & JOB TITLE =================
        $res = mysqli_query($conn, "
            SELECT j.uid AS company_uid, j.title AS job_title, c.cname AS company_name
            FROM job j
            LEFT JOIN company c ON j.uid = c.uid
            WHERE j.jid=$jid
        ");

        if($row = mysqli_fetch_assoc($res)){
            $company_uid = $row['company_uid'];
            $job_title = $row['job_title'];
            $company_name = $row['company_name'];

            // ================= INSERT NOTIFICATION =================
            $message = "Company  " .$company_name.": Your job ".$job_title." has been Approved by admin.";
            mysqli_query($conn, "
                INSERT INTO notifications (uid, message, is_read)
                VALUES ('$company_uid', '$message', 0)
            ") or die(mysqli_error($conn));
        }

        // Regenerate CSRF after successful operation
        regenerateCSRFToken();
    }
}

// Redirect back to jobs page
header("Location: jobs.php");
exit;
?>