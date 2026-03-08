<?php
session_start();

/* Prevent browser caching */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* Session timeout limit */
$timeout = 600; // 10 minutes

/* Check session timeout */
if(isset($_SESSION['last_activity'])){

    $inactive_time = time() - $_SESSION['last_activity'];

    if($inactive_time > $timeout){
        session_unset();
        session_destroy();

        header("Location: ../auth/login.php?msg=timeout");
        exit();
    }
}

/* Update last activity */
$_SESSION['last_activity'] = time();

/* Check admin login */
if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}
?>