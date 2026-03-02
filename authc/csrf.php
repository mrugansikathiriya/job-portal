<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Generate Token */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/* Validate Token */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

/* Regenerate Token After Success */
function regenerateCSRFToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>