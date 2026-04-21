<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";
require "../auth/session_check.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $jid = intval($_POST['jid']);
    $sid = intval($_POST['sid']);

    if(!isset($_FILES['resume']) || $_FILES['resume']['error'] != 0){
        die("Resume upload failed");
    }

    $allowed = ['pdf','doc','docx'];
    $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));

    if(!in_array($ext,$allowed)){
        die("Invalid file type");
    }

    $resumeName = time()."_".uniqid().".".$ext;
    $target = "uploads/".$resumeName;

    if(!move_uploaded_file($_FILES['resume']['tmp_name'],$target)){
        die("File upload failed");
    }

    /* Prevent duplicate application */
    $check = mysqli_query($conn,
    "SELECT 1 FROM application WHERE jid=$jid AND sid=$sid");

    if(mysqli_num_rows($check) > 0){
        die("You already applied for this job.");
    }

    /* Insert application */
    $insert = "INSERT INTO application (jid, sid, resume) 
    VALUES ($jid, $sid, '$resumeName')";

    if(mysqli_query($conn,$insert)){

    $aid = mysqli_insert_id($conn);

    mysqli_query($conn,"UPDATE job SET applicant = applicant + 1 WHERE jid=$jid");

    header("Location: test.php?aid=".$aid);
    exit();

    }else{
    echo "Error: " . mysqli_error($conn);
    }

    }
    ?>