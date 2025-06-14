<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Log activity if user is logged in
if (is_logged_in()) {
    $sql = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, 'logout', 'User logged out', ?, ?)";
    execute_query($sql, 'iss', [
        $_SESSION['user_id'],
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    ]);
}

// Destroy session
destroy_session();

// Redirect to login page
header('Location: login.php');
exit;
?> 