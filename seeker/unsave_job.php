<?php
session_start();
require "../config/db.php";
require "../auth/session_check.php";

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

$jid = $_POST['jid'];
$uid = $_SESSION['uid'];

mysqli_query($conn,"DELETE FROM saved_job WHERE uid='$uid' AND jid='$jid'");

header("Location: find_job.php");
exit;
?>