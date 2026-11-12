<?php
/**
 * Logout Handler
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */

session_start();

// Unset all session variables
$_SESSION = [];

// Destroy session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to login page with notice
header("Location: login.php?msg=logout");
exit;
