<?php
require_once '../config/database.php';
require_once '../config/session.php';

$pdo = Database::getInstance()->getConnection();

// Require admin access
if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'admin') {
            header('Location: ../login/login.php');
            exit;
        }
    }
}
requireAdmin();

$shipment_id = $_GET['id'] ?? null;

if (!$shipment_id) {
    header('Location: dashboard.php?view=shipments');
    exit;
}

// Fetch shipment details for confirmation
$stmt = $pdo->prepare("SELECT tracking_number FROM shipments WHERE id = ?");
$stmt->execute([$shipment_id]);
$shipment = $stmt->fetch();

if (!$shipment) {
    $_SESSION['error'] = 'Shipment not found.';
    header('Location: dashboard.php?view=shipments');
    exit;
}

// Delete shipment and related data
try {
    $pdo->beginTransaction();
    
    // Delete tracking records
    $stmt = $pdo->prepare("DELETE FROM shipment_tracking WHERE shipment_id = ?");
    $stmt->execute([$shipment_id]);
    
    // Delete shipment details
    $stmt = $pdo->prepare("DELETE FROM shipment_details WHERE shipment_id = ?");
    $stmt->execute([$shipment_id]);
    
    // Delete the shipment
    $stmt = $pdo->prepare("DELETE FROM shipments WHERE id = ?");
    $stmt->execute([$shipment_id]);
    
    $pdo->commit();
    
    $_SESSION['success'] = 'Shipment deleted successfully.';
    header('Location: dashboard.php?view=shipments');
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Failed to delete shipment. Please try again.';
    header('Location: dashboard.php?view=shipments');
    exit;
}
?>
