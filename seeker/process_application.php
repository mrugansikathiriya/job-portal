<?php
session_start();
include("connection.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $jid = intval($_POST['jid']);
    $sid = intval($_POST['sid']);

    /* Resume upload */
    $resumeName = time() . "_" . $_FILES['resume']['name'];
    $target = "uploads/" . $resumeName;

    move_uploaded_file($_FILES['resume']['tmp_name'], $target);

    /* Insert into application table */
    $insert = "INSERT INTO application (jid, sid, resume) 
               VALUES ($jid, $sid, '$resumeName')";

    if(mysqli_query($conn,$insert)){

        $aid = mysqli_insert_id($conn);

        /* Increase applicant count */
        mysqli_query($conn,
        "UPDATE job SET applicant = applicant + 1 WHERE jid=$jid");

        /* Redirect to test */
        header("Location: test.php?aid=".$aid);
        exit();

    }else{
        echo "Error: " . mysqli_error($conn);
    }
}
?>