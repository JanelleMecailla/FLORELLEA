<?php
// 1. Initialize session environment
session_start();

// 2. Unset all active session variables (clears admin and user sessions)
$_SESSION = array();

// 3. Clear session cookie from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 4. Destroy the session on the server
session_destroy();

// 5. Prevent browser back-button caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache");                         // HTTP 1.0
header("Expires: 0");                                // Proxies

// 6. Redirect back to login page
header("Location: login.php");
exit;
?>