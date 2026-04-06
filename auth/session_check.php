<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "../config/db.php";

if (isset($_SESSION['uid'])) {

    $uid = $_SESSION['uid'];

    $result = mysqli_query($conn, 
        "SELECT status FROM users WHERE uid='$uid'");

    if ($result && mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        //  IF BLOCKED → FORCE LOGOUT
        if ($user['status'] != 'active') {

            session_unset();
            session_destroy();

            // delete remember me cookie
            if (isset($_COOKIE['remember_token'])) {
                setcookie("remember_token", "", time() - 3600, "/");
            }

            header("Location: ../auth/login.php?error=blocked");
            exit();
        }
    }
}
?>