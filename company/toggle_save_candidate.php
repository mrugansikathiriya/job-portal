<?php
session_start();
require "../config/db.php";
require "../authc/csrf.php";

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'company'){
    exit();
}

// ✅ POST only
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    die("Invalid request");
}

// ✅ CSRF validation
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    die("Invalid CSRF Token");
}

$uid = $_SESSION['uid'];
$sid = intval($_POST['sid']);

// get cid
$res = mysqli_query($conn, "SELECT cid FROM company WHERE uid='$uid'");
$row = mysqli_fetch_assoc($res);
$cid = $row['cid'];

// check saved
$check = mysqli_query($conn, 
    "SELECT * FROM saved_candidate WHERE cid='$cid' AND sid='$sid'"
);

if(mysqli_num_rows($check) > 0){
    mysqli_query($conn, 
        "DELETE FROM saved_candidate WHERE cid='$cid' AND sid='$sid'"
    );
} else {
    mysqli_query($conn, 
        "INSERT INTO saved_candidate (cid, sid) VALUES ('$cid','$sid')"
    );
}

// regenerate token
regenerateCSRFToken();

// redirect back
header("Location: ".$_SERVER['HTTP_REFERER']);
exit();