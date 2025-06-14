<?php
/**
 * Common utility functions for the application
 */

/**
 * Sanitize user input
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if user is logged in
 */
// function is_logged_in() {
//     return isset($_SESSION['user_id']);
// }

/**
 * Check if user is admin
 */
// function is_admin() {
//     return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
// }

/**
 * Redirect to specified URL
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Display flash message
 */
// function set_flash_message($type, $message) {
//     $_SESSION['flash_message'] = [
//         'type' => $type,
//         'message' => $message
//     ];
// }

/**
 * Get and clear flash message
 */
// function get_flash_message() {
//     if (isset($_SESSION['flash_message'])) {
//         $message = $_SESSION['flash_message'];
//         unset($_SESSION['flash_message']);
//         return $message;
//     }
//     return null;
// }

/**
 * Format date
 */
function format_date($date) {
    return date('d M Y H:i', strtotime($date));
}

/**
 * Generate random string
 */
function generate_random_string($length = 10) {
    return bin2hex(random_bytes($length));
}

/**
 * Upload file
 */
function upload_file($file, $destination) {
    $target_dir = "uploads/" . $destination . "/";
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = generate_random_string() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    // Check if file is valid
    if (!in_array($file_extension, ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'])) {
        return false;
    }

    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return false;
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $new_filename;
    }

    return false;
}

/**
 * Log activity
 */
function log_activity($user_id, $action, $details = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    return $stmt->execute();
}

/**
 * Get user details
 */
function get_user_details($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Check if shipment exists
 */
function shipment_exists($shipment_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM shipments WHERE id = ?");
    $stmt->bind_param("i", $shipment_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

/**
 * Get shipment details
 */
function get_shipment_details($shipment_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM shipments WHERE id = ?");
    $stmt->bind_param("i", $shipment_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Check if user has permission to access shipment
 */
function can_access_shipment($user_id, $shipment_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM shipments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $shipment_id, $user_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0 || is_admin();
}