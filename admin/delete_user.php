<?php
include("../config/db.php");

$uid = $_GET['uid'];
mysqli_query($conn, "DELETE FROM users WHERE uid=$uid;");

header("Location: users.php");
?>