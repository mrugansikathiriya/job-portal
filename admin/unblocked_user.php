    <?php
include("../config/db.php");

$uid = $_GET['uid'];
mysqli_query($conn, "UPDATE users SET status='active' WHERE uid=$uid;");

header("Location: users.php");
?>