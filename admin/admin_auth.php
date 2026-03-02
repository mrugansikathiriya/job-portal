<?php
session_start();

if(!isset($_SESSION['uid']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}
?>