<?php
session_start();
require "../config/db.php";

// Check if job id exists
if(isset($_GET['jid']) && is_numeric($_GET['jid'])) {

    $jid = intval($_GET['jid']);

    // Delete job
    $delete = mysqli_query($conn, "DELETE FROM job WHERE jid = $jid");

    if($delete){
        header("Location: jobs.php?msg=deleted");
        exit;
    } else {
        echo "Error deleting job.";
    }

} else {
    header("Location: jobs.php");
    exit;
}
?>