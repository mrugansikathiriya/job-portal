<?php
require "../config/db.php";

if(isset($_GET['jid']) && is_numeric($_GET['jid'])) {

    $jid = intval($_GET['jid']);

    mysqli_query($conn, "
        UPDATE job 
        SET is_approve='rejected' 
        WHERE jid=$jid
    ");
}

header("Location: jobs.php");
exit;
?>