<?php
session_start();

// Unset all user session variables
unset($_SESSION['user_logged_in']);
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);

// Destroy the session entirely
session_destroy();

// Redirect back to the login page
header('Location: login.php');
exit;
?>