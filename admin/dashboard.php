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

$user = getUserData();

// Get current view
$view = $_GET['view'] ?? 'dashboard';

// Handle success and error messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';

// Clear session messages
unset($_SESSION['success']);
unset($_SESSION['error']);

// Get statistics
$stats = [
    'total_users' => 0,
    'total_shipments' => 0,
    'pending_shipments' => 0,
    'delivered_shipments' => 0,
    'recent_shipments' => [],
    'recent_users' => [],
    'unread_messages' => 0,
    'new_applications' => 0
];

// Get total users
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$stats['total_users'] = $stmt->fetch()['count'];

// Get total shipments
$stmt = $pdo->query("SELECT COUNT(*) as count FROM shipments");
$stats['total_shipments'] = $stmt->fetch()['count'];

// Get pending shipments
$stmt = $pdo->query("SELECT COUNT(*) as count FROM shipments WHERE status = 'Pending'");
$stats['pending_shipments'] = $stmt->fetch()['count'];

// Get delivered shipments
$stmt = $pdo->query("SELECT COUNT(*) as count FROM shipments WHERE status = 'Delivered'");
$stats['delivered_shipments'] = $stmt->fetch()['count'];

// Get unread messages count
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM general_messages WHERE status = 'new'");
    $stats['unread_messages'] = $stmt->fetch()['count'];
} catch (PDOException $e) {
    // Table might not exist yet
    $stats['unread_messages'] = 0;
}

// Get new job applications count
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new'");
    $stats['new_applications'] = $stmt->fetch()['count'];
} catch (PDOException $e) {
    // Table might not exist yet
    $stats['new_applications'] = 0;
}

// Get recent shipments
$stmt = $pdo->query("
    SELECT s.*, u.username as user_name 
    FROM shipments s 
    JOIN users u ON s.user_id = u.id 
    ORDER BY s.created_at DESC 
    LIMIT 5
");
$stats['recent_shipments'] = $stmt->fetchAll();

// Get recent users
$stmt = $pdo->query("
    SELECT * FROM users 
    WHERE role = 'user' 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stats['recent_users'] = $stmt->fetchAll();

// Get all shipments for shipments view
$shipments = [];
if ($view === 'shipments') {
    $stmt = $pdo->query("
        SELECT s.*, u.username as user_name 
        FROM shipments s 
        JOIN users u ON s.user_id = u.id 
        ORDER BY s.created_at DESC
    ");
    $shipments = $stmt->fetchAll();
}

// Get all users for users view
$users = [];
if ($view === 'users') {
    $stmt = $pdo->query("
        SELECT u.*, 
               (SELECT COUNT(*) FROM shipments WHERE user_id = u.id) as shipment_count 
        FROM users u 
        ORDER BY u.created_at DESC
    ");
    $users = $stmt->fetchAll();
}

// Get all messages for messages view
$messages = [];
if ($view === 'messages') {
    try {
        $stmt = $pdo->query("
            SELECT * FROM general_messages
            ORDER BY created_at DESC
        ");
        $messages = $stmt->fetchAll();
        
        // Mark messages as read if they were new
        $pdo->exec("UPDATE general_messages SET status = 'read' WHERE status = 'new'");
    } catch (PDOException $e) {
        // Table might not exist yet
        $error = "Tabel pesan belum dibuat. Silakan jalankan script SQL untuk membuat tabel.";
    }
}

// Get all job applications for applications view
$applications = [];
if ($view === 'applications') {
    try {
        $stmt = $pdo->query("
            SELECT * FROM contact_messages
            ORDER BY created_at DESC
        ");
        $applications = $stmt->fetchAll();
        
        // Mark applications as read if they were new
        $pdo->exec("UPDATE contact_messages SET status = 'read' WHERE status = 'new'");
    } catch (PDOException $e) {
        // Table might not exist yet
        $error = "Tabel lamaran kerja belum dibuat. Silakan jalankan script SQL untuk membuat tabel.";
    }
}

// Handle message reply
if (isset($_POST['reply_message'])) {
    $message_id = $_POST['message_id'];
    
    // Update message status to replied
    $stmt = $pdo->prepare("UPDATE general_messages SET status = 'replied' WHERE id = ?");
    $stmt->execute([$message_id]);
    
    $success = "Status pesan berhasil diubah menjadi 'Sudah Dibalas'";
}

// Handle application status update
if (isset($_POST['update_application'])) {
    $application_id = $_POST['application_id'];
    $new_status = $_POST['new_status'];
    
    // Update application status
    $stmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $application_id]);
    
    $success = "Status lamaran berhasil diperbarui";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Logistics Maritime</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #f8f9fa;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --dark-color: #343a40;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar styles */
        .sidebar {
            width: 250px;
            background-color: #fff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
            position: fixed;
            height: 100vh;
            z-index: 100;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sidebar-header span {
            font-weight: bold;
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(0, 102, 204, 0.1);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .user-info {
            padding: 20px;
            border-top: 1px solid #eee;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .user-info .user-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .user-info .user-role {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .btn-logout {
            display: inline-block;
            padding: 8px 12px;
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background-color: #e9ecef;
        }

        /* Main content styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--dark-color);
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .bg-primary {
            background-color: var(--primary-color);
        }

        .bg-success {
            background-color: var(--success-color);
        }

        .bg-warning {
            background-color: var(--warning-color);
        }

        .bg-info {
            background-color: var(--info-color);
        }

        .bg-danger {
            background-color: var(--danger-color);
        }

        .card-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .card-label {
            color: #666;
            font-size: 0.9rem;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .data-table th,
        .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .data-table th {
            background-color: var(--secondary-color);
            font-weight: bold;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-in-transit {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-new {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-read {
            background-color: #d4edda;
            color: #155724;
        }

        .status-replied {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .role-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }

        .role-admin {
            background-color: #e7f3ff;
            color: #0066cc;
        }

        .role-user {
            background-color: #f0f9ff;
            color: #0891b2;
        }

        .action-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            margin-right: 5px;
            display: inline-block;
        }

        .view-btn {
            background-color: var(--info-color);
            color: white;
        }

        .view-btn:hover {
            background-color: #138496;
        }

        .edit-btn {
            background-color: var(--warning-color);
            color: #000;
        }

        .edit-btn:hover {
            background-color: #e0a800;
        }

        .delete-btn {
            background-color: var(--danger-color);
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .add-btn {
            background-color: var(--success-color);
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .add-btn:hover {
            background-color: #218838;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .tab-navigation {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 10px 15px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .tab-btn:hover, .tab-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .tab-btn .badge {
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .sidebar.active {
                width: 250px;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }

            .data-table {
                display: block;
                overflow-x: auto;
            }
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #333;
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
        }

        /* Delete confirmation modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--dark-color);
        }

        .close-modal {
            font-size: 1.5rem;
            cursor: pointer;
            color: #aaa;
        }

        .close-modal:hover {
            color: #333;
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-confirm {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }

        /* Message detail styles */
        .message-detail {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .message-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .message-meta {
            color: #666;
            font-size: 0.9rem;
        }

        .message-content {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .message-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* File preview */
        .file-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .file-icon {
            font-size: 2rem;
            color: #dc3545;
        }

        .file-info {
            flex: 1;
        }

        .file-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .file-actions {
            display: flex;
            gap: 10px;
        }

        /* Status dropdown */
        .status-select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        /* Truncate long text */
        .truncate {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon">
                    <img src="../logistikmaritim/image/logo.png" alt="logo" />
                </div>
                <span>Admin Dashboard</span>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php" class="menu-item <?php echo $view === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="dashboard.php?view=shipments" class="menu-item <?php echo $view === 'shipments' ? 'active' : ''; ?>">
                    <i class="fas fa-shipping-fast"></i> Shipments
                </a>
                <a href="dashboard.php?view=users" class="menu-item <?php echo $view === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="dashboard.php?view=messages" class="menu-item <?php echo $view === 'messages' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Messages
                    <?php if ($stats['unread_messages'] > 0): ?>
                        <span class="badge"><?php echo $stats['unread_messages']; ?></span>
                    <?php endif; ?>
                </a>
                <a href="dashboard.php?view=applications" class="menu-item <?php echo $view === 'applications' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> Job Applications
                    <?php if ($stats['new_applications'] > 0): ?>
                        <span class="badge"><?php echo $stats['new_applications']; ?></span>
                    <?php endif; ?>
                </a>
                <a href="new-shipment.php" class="menu-item">
                    <i class="fas fa-plus-circle"></i> New Shipment
                </a>

            </div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                <div class="user-role">Administrator</div>
                <a href="../login/logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">
                    <?php if ($view === 'dashboard'): ?>
                        <i class="fas fa-tachometer-alt"></i> Dashboard Overview
                    <?php elseif ($view === 'shipments'): ?>
                        <i class="fas fa-shipping-fast"></i> Shipment Management
                    <?php elseif ($view === 'users'): ?>
                        <i class="fas fa-users"></i> User Management
                    <?php elseif ($view === 'messages'): ?>
                        <i class="fas fa-envelope"></i> Message Management
                    <?php elseif ($view === 'applications'): ?>
                        <i class="fas fa-file-alt"></i> Job Applications
                    <?php endif; ?>
                </h1>
                <div>
                    <?php if ($view === 'shipments'): ?>
                        <a href="new-shipment.php" class="add-btn">
                            <i class="fas fa-plus"></i> New Shipment
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($view === 'dashboard'): ?>
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Total Users</h2>
                            <div class="card-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="card-value"><?php echo $stats['total_users']; ?></div>
                        <div class="card-label">Registered customers</div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Total Shipments</h2>
                            <div class="card-icon bg-info">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                        </div>
                        <div class="card-value"><?php echo $stats['total_shipments']; ?></div>
                        <div class="card-label">All time shipments</div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Pending</h2>
                            <div class="card-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="card-value"><?php echo $stats['pending_shipments']; ?></div>
                        <div class="card-label">Awaiting processing</div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Delivered</h2>
                            <div class="card-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="card-value"><?php echo $stats['delivered_shipments']; ?></div>
                        <div class="card-label">Completed shipments</div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">New Messages</h2>
                            <div class="card-icon bg-danger">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                        <div class="card-value"><?php echo $stats['unread_messages']; ?></div>
                        <div class="card-label">Unread messages</div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Job Applications</h2>
                            <div class="card-icon bg-primary">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                        <div class="card-value"><?php echo $stats['new_applications']; ?></div>
                        <div class="card-label">New applications</div>
                    </div>
                </div>

                <!-- Recent Shipments -->
                <h2 class="section-title">
                    <i class="fas fa-clock"></i> Recent Shipments
                </h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Customer</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($stats['recent_shipments']) > 0): ?>
                            <?php foreach ($stats['recent_shipments'] as $shipment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shipment['tracking_number']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['sender_address']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['receiver_address']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($shipment['status']); ?>">
                                            <?php echo htmlspecialchars($shipment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($shipment['created_at'])); ?></td>
                                    <td>
                                        <a href="edit-shipment.php?id=<?php echo $shipment['id']; ?>" class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No recent shipments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Recent Users -->
                <h2 class="section-title">
                    <i class="fas fa-user-clock"></i> Recent Users
                </h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($stats['recent_users']) > 0): ?>
                            <?php foreach ($stats['recent_users'] as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="role-badge role-<?php echo $user['role']; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="view-user.php?id=<?php echo $user['id']; ?>" class="action-btn view-btn">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No recent users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php elseif ($view === 'shipments'): ?>
                <!-- Shipments Management
                <div class="search-container">
                    <input type="text" id="shipmentSearch" class="search-input" placeholder="Search shipments by tracking number, customer, origin, or destination...">
                </div> -->

                <table class="data-table" id="shipmentsTable">
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Customer</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($shipments) > 0): ?>
                            <?php foreach ($shipments as $shipment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shipment['tracking_number']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['sender_address']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['receiver_address']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($shipment['status']); ?>">
                                            <?php echo htmlspecialchars($shipment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($shipment['created_at'])); ?></td>
                                    <td>
                                        <a href="edit-shipment.php?id=<?php echo $shipment['id']; ?>" class="action-btn edit-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="action-btn delete-btn" title="Delete" 
                                           onclick="confirmDelete('shipment', <?php echo $shipment['id']; ?>, '<?php echo htmlspecialchars($shipment['tracking_number']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No shipments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php elseif ($view === 'users'): ?>
                <!-- Users Management 
                <div class="search-container">
                    <input type="text" id="userSearch" class="search-input" placeholder="Search users by username or email...">
                </div> -->

                <table class="data-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Shipments</th>
                            <th>Registered Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="role-badge role-<?php echo $user['role']; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $user['shipment_count']; ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="view-user.php?id=<?php echo $user['id']; ?>" class="action-btn view-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="action-btn edit-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <a href="#" class="action-btn delete-btn" title="Delete" 
                                               onclick="confirmDelete('user', <?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            <?php elseif ($view === 'messages'): ?>
                <!-- Messages Management 
                <div class="search-container">
                    <input type="text" id="messageSearch" class="search-input" placeholder="Search messages by name, email, or content...">
                </div>  -->

                <table class="data-table" id="messagesTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($messages) > 0): ?>
                            <?php foreach ($messages as $message): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($message['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                                    <td><?php echo htmlspecialchars($message['phone']); ?></td>
                                    <td class="truncate"><?php echo htmlspecialchars($message['pesan']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $message['status']; ?>">
                                            <?php 
                                                switch($message['status']) {
                                                    case 'new': echo 'Baru'; break;
                                                    case 'read': echo 'Dibaca'; break;
                                                    case 'replied': echo 'Dibalas'; break;
                                                    default: echo ucfirst($message['status']);
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($message['created_at'])); ?></td>
                                    <td>
                                        <button class="action-btn view-btn" title="View" 
                                                onclick="viewMessage('<?php echo htmlspecialchars(addslashes($message['nama'])); ?>', 
                                                                    '<?php echo htmlspecialchars(addslashes($message['email'])); ?>', 
                                                                    '<?php echo htmlspecialchars(addslashes($message['phone'])); ?>', 
                                                                    '<?php echo htmlspecialchars(addslashes($message['pesan'])); ?>', 
                                                                    '<?php echo date('Y-m-d H:i', strtotime($message['created_at'])); ?>', 
                                                                    '<?php echo $message['id']; ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($message['status'] !== 'replied'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                <button type="submit" name="reply_message" class="action-btn edit-btn" title="Mark as Replied">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No messages found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            <?php elseif ($view === 'applications'): ?>
                <!-- Job Applications Management 
                <div class="search-container">
                    <input type="text" id="applicationSearch" class="search-input" placeholder="Search applications by name, email, or position...">
                </div>  -->

                <table class="data-table" id="applicationsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Position</th>
                            <th>Resume</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($applications) > 0): ?>
                            <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($application['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($application['email']); ?></td>
                                    <td><?php echo htmlspecialchars($application['phone']); ?></td>
                                    <td>
                                        <?php 
                                            $positions = [
                                                'manajer-logistik' => 'Manajer Logistik Maritim',
                                                'operator-crane' => 'Operator Crane Pelabuhan',
                                                'supply-chain' => 'Supply Chain Analyst',
                                                'marketing' => 'Marketing Staff',
                                                'dokumentasi' => 'Dokumentasi Logistik',
                                                'perencana-rute' => 'Perencana Rute Kapal'
                                            ];
                                            echo isset($positions[$application['position']]) ? 
                                                $positions[$application['position']] : 
                                                htmlspecialchars($application['position']);
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($application['resume_filename']): ?>
                                            <a href="../public/uploads/resumes/<?php echo htmlspecialchars($application['resume_filename']); ?>" 
                                               target="_blank" class="action-btn view-btn" title="View Resume">
                                                <i class="fas fa-file-pdf"></i> View
                                            </a>
                                        <?php else: ?>
                                            <span>No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $application['status']; ?>">
                                            <?php 
                                                switch($application['status']) {
                                                    case 'new': echo 'Baru'; break;
                                                    case 'read': echo 'Dibaca'; break;
                                                    case 'replied': echo 'Diproses'; break;
                                                    default: echo ucfirst($application['status']);
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($application['created_at'])); ?></td>
                                    <td>
                                        <button class="action-btn view-btn" title="View Details" 
                                                onclick="viewApplication('<?php echo htmlspecialchars(addslashes($application['nama'])); ?>', 
                                                                       '<?php echo htmlspecialchars(addslashes($application['email'])); ?>', 
                                                                       '<?php echo htmlspecialchars(addslashes($application['phone'])); ?>', 
                                                                       '<?php echo htmlspecialchars(addslashes($application['position'])); ?>', 
                                                                       '<?php echo htmlspecialchars(addslashes($application['resume_filename'])); ?>', 
                                                                       '<?php echo $application['status']; ?>', 
                                                                       '<?php echo date('Y-m-d H:i', strtotime($application['created_at'])); ?>', 
                                                                       '<?php echo $application['id']; ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No job applications found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Deletion</h3>
                <span class="close-modal" onclick="closeModal('deleteModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p id="deleteMessage"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-confirm">Delete</a>
            </div>
        </div>
    </div>
    
    <!-- View Message Modal -->
    <div id="viewMessageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Message Details</h3>
                <span class="close-modal" onclick="closeModal('viewMessageModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="message-detail">
                    <div class="message-header">
                        <div class="message-title" id="messageTitle">Message from <span id="messageSender"></span></div>
                        <div class="message-meta" id="messageDate"></div>
                    </div>
                    <div class="message-content">
                        <p><strong>Email:</strong> <span id="messageEmail"></span></p>
                        <p><strong>Phone:</strong> <span id="messagePhone"></span></p>
                        <p><strong>Message:</strong></p>
                        <p id="messageContent"></p>
                    </div>
                    <div class="message-actions">
                        <form method="POST">
                            <input type="hidden" name="message_id" id="messageId">
                            <button type="submit" name="reply_message" class="btn btn-confirm">Mark as Replied</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Application Modal -->
    <div id="viewApplicationModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3 class="modal-title">Application Details</h3>
                <span class="close-modal" onclick="closeModal('viewApplicationModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="message-detail">
                    <div class="message-header">
                        <div class="message-title" id="applicationTitle">Application from <span id="applicationSender"></span></div>
                        <div class="message-meta" id="applicationDate"></div>
                    </div>
                    <div class="message-content">
                        <p><strong>Email:</strong> <span id="applicationEmail"></span></p>
                        <p><strong>Phone:</strong> <span id="applicationPhone"></span></p>
                        <p><strong>Position:</strong> <span id="applicationPosition"></span></p>
                        
                        <div id="resumePreview" class="file-preview">
                            <div class="file-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="file-info">
                                <div class="file-name" id="resumeFilename"></div>
                            </div>
                            <div class="file-actions">
                                <a href="#" id="viewResumeBtn" class="action-btn view-btn" target="_blank">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="#" id="downloadResumeBtn" class="action-btn edit-btn" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="application_id" id="applicationId">
                            <div style="margin-top: 20px;">
                                <label for="applicationStatus"><strong>Status:</strong></label>
                                <select name="new_status" id="applicationStatus" class="status-select">
                                    <option value="new">Baru</option>
                                    <option value="read">Dibaca</option>
                                    <option value="replied">Diproses</option>
                                </select>
                            </div>
                            <div class="message-actions" style="margin-top: 20px;">
                                <button type="submit" name="update_application" class="btn btn-confirm">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Search functionality for shipments
        document.getElementById('shipmentSearch')?.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('shipmentsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length - 1; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(searchValue) > -1) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });

        // Search functionality for users
        document.getElementById('userSearch')?.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('usersTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < 2; j++) { // Only search in username and email columns
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(searchValue) > -1) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        // Search functionality for messages
        document.getElementById('messageSearch')?.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('messagesTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < 4; j++) { // Search in name, email, phone, and message columns
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(searchValue) > -1) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        // Search functionality for applications
        document.getElementById('applicationSearch')?.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('applicationsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < 4; j++) { // Search in name, email, phone, and position columns
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(searchValue) > -1) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });

        // Delete confirmation modal
        function confirmDelete(type, id, name) {
            const modal = document.getElementById('deleteModal');
            const message = document.getElementById('deleteMessage');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            
            if (type === 'shipment') {
                message.textContent = `Are you sure you want to delete shipment with tracking number "${name}"? This action cannot be undone.`;
                confirmBtn.href = `delete-shipment.php?id=${id}`;
            } else if (type === 'user') {
                message.textContent = `Are you sure you want to delete user "${name}" and all associated data? This action cannot be undone.`;
                confirmBtn.href = `delete-user.php?id=${id}`;
            }
            
            modal.style.display = 'block';
        }
        
        // View message modal
        function viewMessage(name, email, phone, content, date, id) {
            document.getElementById('messageSender').textContent = name;
            document.getElementById('messageEmail').textContent = email;
            document.getElementById('messagePhone').textContent = phone;
            document.getElementById('messageContent').textContent = content;
            document.getElementById('messageDate').textContent = date;
            document.getElementById('messageId').value = id;
            
            document.getElementById('viewMessageModal').style.display = 'block';
        }
        
        // View application modal
        function viewApplication(name, email, phone, position, resume, status, date, id) {
            const positions = {
                'manajer-logistik': 'Manajer Logistik Maritim',
                'operator-crane': 'Operator Crane Pelabuhan',
                'supply-chain': 'Supply Chain Analyst',
                'marketing': 'Marketing Staff',
                'dokumentasi': 'Dokumentasi Logistik',
                'perencana-rute': 'Perencana Rute Kapal'
            };
            
            document.getElementById('applicationSender').textContent = name;
            document.getElementById('applicationEmail').textContent = email;
            document.getElementById('applicationPhone').textContent = phone;
            document.getElementById('applicationPosition').textContent = positions[position] || position;
            document.getElementById('applicationDate').textContent = date;
            document.getElementById('applicationId').value = id;
            document.getElementById('applicationStatus').value = status;
            
            if (resume) {
                document.getElementById('resumePreview').style.display = 'flex';
                document.getElementById('resumeFilename').textContent = resume;
                document.getElementById('viewResumeBtn').href = '../public/uploads/resumes/' + resume;
                document.getElementById('downloadResumeBtn').href = '../public/uploads/resumes/' + resume;
            } else {
                document.getElementById('resumePreview').style.display = 'none';
            }
            
            document.getElementById('viewApplicationModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
