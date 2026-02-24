<?php
require "../config/db.php";

if(isset($_GET['jid']) && is_numeric($_GET['jid'])) {

    $jid = intval($_GET['jid']);

    $update = mysqli_query($conn, "
        UPDATE job 
        SET is_approve='approved' 
        WHERE jid=$jid
    ");

   
}

header("Location: jobs.php");
exit;
?>