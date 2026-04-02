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


    mysqli_query($conn, "
        UPDATE job 
        SET is_approve='rejected' 
        WHERE jid=$jid
    ");
              regenerateCSRFToken();


}
}

header("Location: jobs.php");
exit;
?>