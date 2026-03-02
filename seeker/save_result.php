<?php
session_start();
include("connection.php");

if(!isset($_SESSION['aid'])){
    exit("Invalid access");
}

$aid = intval($_SESSION['aid']);
$score = intval($_POST['score']);

mysqli_query($conn,
"UPDATE application SET score=$score WHERE aid=$aid");

echo "Test Submitted Successfully!";
?>