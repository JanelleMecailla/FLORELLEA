<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. If an admin is logged in, immediately allow access to any page
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    return; // Bypass remaining auth checks
}

// 2. If no regular user is logged in, redirect to register/login
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: register.php");
    exit();
}
?>