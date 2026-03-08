<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";

/* CSRF Validation */
if(!validateCSRFToken($_POST['csrf_token'])){
    die("Invalid CSRF Token");
}

/* Check application id */
if(!isset($_SESSION['aid'])){
    die("Application ID missing");
}

$aid = $_SESSION['aid'];
$score = intval($_POST['score']);

/* Update score and attempt */
$query = "
UPDATE application
SET score='$score',
attempt = attempt + 1
WHERE aid='$aid'
";

$result = mysqli_query($conn,$query);

if($result){

    /* If failed */
    if($score == 0){

        $_SESSION['fail_msg'] = "Application not submitted. You failed the test. Please attempt again.";

        echo "fail";

    }
    /* If passed */
    else{

        $_SESSION['success_msg'] = "Application submitted successfully!";

        echo "success";

    }

}else{

    echo "Error: ".mysqli_error($conn);

}
?>