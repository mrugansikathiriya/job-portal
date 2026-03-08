<?php
session_start();

require "../config/db.php";

/* =========================
   REMOVE REMEMBER TOKEN
========================= */
if (isset($_SESSION['uid'])) {

    $uid = $_SESSION['uid'];

    mysqli_query($conn,
        "UPDATE users SET remember_token=NULL WHERE uid='$uid'"
    );
}

/* =========================
   DELETE REMEMBER COOKIE
========================= */
if (isset($_COOKIE['remember_token'])) {
    setcookie("remember_token", "", time() - 3600, "/", "", false, true);
}

/* =========================
   DESTROY SESSION
========================= */
$_SESSION = [];

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

/* =========================
   REDIRECT TO LOGIN
========================= */
header("Location: login.php");
exit();
?>