<?php
require "admin_auth.php";    

require "../config/db.php";
require "../authc/csrf.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }
    // Validate Job ID
    if (isset($_POST['jid']) && is_numeric($_POST['jid'])) {

        $jid = intval($_POST['jid']);

    // Delete job
    $delete = mysqli_query($conn, "DELETE FROM job WHERE jid = $jid");

    if($delete){
                    regenerateCSRFToken();  
        header("Location: jobs.php?msg=deleted");
        exit;
    } else {
        echo "Error deleting job.";
    }

} else {
    header("Location: jobs.php");
    exit;
}
} else {
    header("Location: jobs.php");
    exit;
}
?>