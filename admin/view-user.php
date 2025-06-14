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

$admin_user = getUserData();
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header('Location: dashboard.php?view=users');
    exit;
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: dashboard.php?view=users');
    exit;
}

// Check if current user has permission to view this user's details
if ($admin_user['role'] !== 'admin' && $user_id != $admin_user['id']) {
    // Redirect if not admin and not viewing own profile
    header('Location: user-dashboard.php');
    exit;
}

// Fetch user's shipments
$stmt = $pdo->prepare("
    SELECT s.*, COUNT(sd.id) as item_count 
    FROM shipments s 
    LEFT JOIN shipment_details sd ON s.id = sd.shipment_id 
    WHERE s.user_id = ? 
    GROUP BY s.id 
    ORDER BY s.created_at DESC
");
$stmt->execute([$user_id]);
$shipments = $stmt->fetchAll();

// Get user statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_shipments FROM shipments WHERE user_id = ?");
$stmt->execute([$user_id]);
$totalShipments = $stmt->fetch()['total_shipments'];

$stmt = $pdo->prepare("SELECT COUNT(*) as delivered_shipments FROM shipments WHERE user_id = ? AND status = 'Delivered'");
$stmt->execute([$user_id]);
$deliveredShipments = $stmt->fetch()['delivered_shipments'];

$stmt = $pdo->prepare("SELECT COUNT(*) as pending_shipments FROM shipments WHERE user_id = ? AND status = 'Pending'");
$stmt->execute([$user_id]);
$pendingShipments = $stmt->fetch()['pending_shipments'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - Admin Dashboard</title>
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
            padding-top: 80px;
        }

        .header {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: bold;
            color: var(--primary-color);
            font-size: 1.25rem;
        }

        .logo-icon img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--dark-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .user-profile {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .user-name {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .user-role {
            font-size: 1rem;
            opacity: 0.9;
        }

        .profile-content {
            padding: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: var(--secondary-color);
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .info-label {
            font-weight: 600;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            font-weight: bold;
            color: var(--dark-color);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            border-top: 4px solid var(--primary-color);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .shipments-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .section-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            background: var(--secondary-color);
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .data-table th {
            background-color: var(--secondary-color);
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-in-transit {
            background: #cce5ff;
            color: #004085;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .role-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .role-admin {
            background: #e7f3ff;
            color: #0066cc;
        }

        .role-user {
            background: #f0f9ff;
            color: #0891b2;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: #000;
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--dark-color);
            border: 2px solid #e0e0e0;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .profile-content {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon">
                    <img src="../logistikmaritim/image/logo.png" alt="logo" />
                </div>
                <span>Logistik Maritime - Admin</span>
            </div>
            <div class="auth-buttons">
                <div class="user-menu">
                    <span>Welcome, <?php echo htmlspecialchars($admin_user['username']); ?></span>
                    <a href="../login/logout.php" class="btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-user"></i>
                User Details
            </h1>
            <div class="breadcrumb">
                <a href="dashboard.php">Dashboard</a>
                <span>/</span>
                <a href="dashboard.php?view=users">Users</a>
                <span>/</span>
                <span><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
        </div>

        <!-- User Profile -->
        <div class="user-profile">
            <div class="profile-header">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                <div class="user-role">
                    <span class="role-badge role-<?php echo $user['role']; ?>">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </div>
            </div>

            <div class="profile-content">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-envelope"></i> Email
                        </span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-calendar-alt"></i> Member Since
                        </span>
                        <span class="info-value"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-clock"></i> Last Updated
                        </span>
                        <span class="info-value"><?php echo date('M j, Y H:i', strtotime($user['updated_at'] ?? $user['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="fas fa-id-badge"></i> User ID
                        </span>
                        <span class="info-value">#<?php echo $user['id']; ?></span>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit User
                    </a>
                    <a href="dashboard.php?view=users" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalShipments; ?></div>
                <div class="stat-label">Total Shipments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $deliveredShipments; ?></div>
                <div class="stat-label">Delivered</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $pendingShipments; ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <!-- User's Shipments -->
        <div class="shipments-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-shipping-fast"></i>
                    User's Shipments
                </h2>
            </div>

            <?php if (count($shipments) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Route</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shipments as $shipment): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($shipment['tracking_number']); ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong>From:</strong> <?php echo htmlspecialchars($shipment['sender_address']); ?><br>
                                        <strong>To:</strong> <?php echo htmlspecialchars($shipment['receiver_address']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($shipment['status']); ?>">
                                        <?php echo htmlspecialchars($shipment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo $shipment['item_count']; ?></strong> items
                                </td>
                                <td><?php echo date('M j, Y H:i', strtotime($shipment['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>No Shipments Found</h3>
                    <p>This user hasn't created any shipments yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
