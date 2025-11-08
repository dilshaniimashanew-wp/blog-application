<?php
/**
 * User Logout
 * Destroys user session and redirects to login page
 */

// Start the session
session_start();

// ============================================
// CLEAR ALL SESSION DATA
// ============================================

// Unset all session variables
$_SESSION = array();

// If session uses cookies, delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session completely
session_destroy();

// ============================================
// REDIRECT TO LOGIN PAGE
// ============================================
header('Location: login.php');
exit();
?>