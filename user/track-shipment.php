<?php
require_once '../config/database.php';
require_once '../config/session.php';

$pdo = Database::getInstance()->getConnection();

// Require login to access
requireLogin();

$user = getUserData();
$error = '';
$shipment = null;
$tracking_history = [];

// Get tracking number from URL
$tracking_number = $_GET['tracking_number'] ?? null;

if ($tracking_number) {
    // Get shipment details
    $stmt = $pdo->prepare("
        SELECT s.*, COUNT(sd.id) as item_count 
        FROM shipments s 
        LEFT JOIN shipment_details sd ON s.id = sd.shipment_id 
        WHERE s.tracking_number = ? 
        GROUP BY s.id
    ");
    $stmt->execute([$tracking_number]);
    $shipment = $stmt->fetch();

    if ($shipment) {
        // Get tracking history
        $stmt = $pdo->prepare("SELECT * FROM shipment_tracking WHERE shipment_id = ? ORDER BY created_at DESC");
        $stmt->execute([$shipment['id']]);
        $tracking_history = $stmt->fetchAll();
    } else {
        $error = "No shipment found with tracking number: " . htmlspecialchars($tracking_number);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Shipment - Logistics Maritime</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #f8f9fa;
            --accent-color: #17a2b8;
            --text-color: #333;
            --light-text: #6c757d;
            --border-color: #dee2e6;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: var(--text-color);
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
            border-bottom: 1px solid var(--border-color);
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
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: var(--text-color);
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
            border-top: 1px solid var(--border-color);
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .user-info .user-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .user-info .user-role {
            color: var(--light-text);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .btn-logout {
            display: inline-block;
            padding: 8px 12px;
            background-color: #f8f9fa;
            color: var(--text-color);
            border: 1px solid var(--border-color);
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
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .tracking-form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .tracking-result {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 30px;
        }

        .shipment-details {
            margin-bottom: 20px;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: bold;
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

        .tracking-timeline {
            margin-top: 20px;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            padding-bottom: 20px;
            border-left: 2px solid var(--border-color);
            margin-left: 10px;
        }

        .timeline-item:last-child {
            border-left: 2px solid transparent;
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: var(--primary-color);
            border: 3px solid white;
        }

        .timeline-date {
            font-size: 0.9rem;
            color: var(--light-text);
            margin-bottom: 5px;
        }

        .timeline-content {
            background-color: var(--secondary-color);
            padding: 15px;
            border-radius: 8px;
        }

        .timeline-title {
            font-weight: bold;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .timeline-title .status-badge {
            margin-left: 10px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .back-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: var(--secondary-color);
            color: var(--text-color);
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .back-btn:hover {
            background-color: #e9ecef;
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

            .detail-row {
                grid-template-columns: 1fr;
            }
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-color);
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
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
                <span>Logistik Maritime</span>
            </div>
            <div class="sidebar-menu">
                <a href="user-dashboard.php" class="menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="track-shipment.php" class="menu-item active">
                    <i class="fas fa-search-location"></i> Track Shipment
                </a>
                <a href="user-dashboard.php#shipments-section" class="menu-item">
                    <i class="fas fa-shipping-fast"></i> My Shipments
                </a>
                <a href="user-dashboard.php#contact-section" class="menu-item">
                    <i class="fas fa-headset"></i> Service Center
                </a>
                <a href="profile.php" class="menu-item">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                <div class="user-role">Customer</div>
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
                <h1 class="page-title">Track Shipment</h1>
                <div></div> <!-- Empty div for flex spacing -->
            </div>

            <!-- Tracking Form -->
            <div class="tracking-form">
                <h2 class="form-title">Enter Tracking Number</h2>
                <form action="track-shipment.php" method="GET">
                    <div class="form-group">
                        <input type="text" name="tracking_number" class="form-control" placeholder="Enter tracking number" value="<?php echo htmlspecialchars($tracking_number ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Track
                    </button>
                </form>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($shipment): ?>
                <div class="tracking-result">
                    <h2 class="section-title">Shipment Information</h2>
                    <div class="shipment-details">
                        <div class="detail-row">
                            <div class="detail-label">Tracking Number:</div>
                            <div><?php echo htmlspecialchars($shipment['tracking_number']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div>
                                <span class="status-badge status-<?php echo strtolower($shipment['status']); ?>">
                                    <?php echo htmlspecialchars($shipment['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Origin:</div>
                            <div><?php echo htmlspecialchars($shipment['sender_address']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Destination:</div>
                            <div><?php echo htmlspecialchars($shipment['receiver_address']); ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Created Date:</div>
                            <div><?php echo date('Y-m-d H:i', strtotime($shipment['created_at'])); ?></div>
                        </div>
                    </div>

                    <h3 class="section-title">Tracking History</h3>
                    <div class="tracking-timeline">
                        <?php if (count($tracking_history) > 0): ?>
                            <?php foreach ($tracking_history as $tracking): ?>
                                <div class="timeline-item">
                                    <div class="timeline-date"><?php echo date('Y-m-d H:i', strtotime($tracking['created_at'])); ?></div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">
                                            <span class="status-badge status-<?php echo strtolower($tracking['status']); ?>">
                                                <?php echo htmlspecialchars($tracking['status']); ?>
                                            </span>
                                        </div>
                                        <div class="timeline-location"><?php echo htmlspecialchars($tracking['location']); ?></div>
                                        <div class="timeline-description"><?php echo htmlspecialchars($tracking['description']); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No tracking history available.</p>
                        <?php endif; ?>
                    </div>

                    <a href="view-shipment.php?id=<?php echo $shipment['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-eye"></i> View Full Details
                    </a>
                </div>
            <?php endif; ?>

            <a href="user-dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>

</html>
