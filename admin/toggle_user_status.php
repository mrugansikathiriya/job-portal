<?php
require "../config/db.php";
require "admin_auth.php";
require "../authc/csrf.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }


    if(isset($_POST['uid']) && is_numeric($_POST['uid'])) {

        $uid = intval($_POST['uid']);
        $action = $_POST['action'];

        if($action === "block"){
            mysqli_query($conn, "UPDATE users SET status='blocked' WHERE uid=$uid");
        } else {
            mysqli_query($conn, "UPDATE users SET status='active' WHERE uid=$uid");
        }

        header("Location: users.php");
        exit;
    }
}
?>