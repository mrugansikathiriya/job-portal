<?php


require "../config/db.php";
require "admin_auth.php";
require "../authc/csrf.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 if (!validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    // Validate UID
    if (isset($_POST['uid']) && is_numeric($_POST['uid'])) {

        $uid = intval($_POST['uid']);

        // Optional: Prevent admin deleting himself
        if ($uid == $_SESSION['uid']) {
            die("You cannot delete yourself.");
        }

        $delete = mysqli_query($conn, "DELETE FROM users WHERE uid = $uid");

        if ($delete) {
            header("Location: users.php?msg=deleted");
            exit;
        } else {
            echo "Error deleting user.";
        }

    } else {
        header("Location: users.php");
        exit;
    }

} else {
    header("Location: users.php");
    exit;
}
?>