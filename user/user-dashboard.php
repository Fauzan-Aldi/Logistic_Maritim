<?php
require_once '../config/database.php';
require_once '../config/session.php';

$pdo = Database::getInstance()->getConnection();

// Require login to access
requireLogin();

$user = getUserData();
$error = '';

// Get user shipments
$stmt = $pdo->prepare("
    SELECT s.*, COUNT(sd.id) as item_count 
    FROM shipments s 
    LEFT JOIN shipment_details sd ON s.id = sd.shipment_id 
    WHERE s.user_id = ? 
    GROUP BY s.id
    ORDER BY s.created_at DESC
");
$stmt->execute([$user['id']]);
$shipments = $stmt->fetchAll();

// Get recent tracking updates
$stmt = $pdo->prepare("
    SELECT st.*, s.tracking_number 
    FROM shipment_tracking st
    JOIN shipments s ON st.shipment_id = s.id
    WHERE s.user_id = ?
    ORDER BY st.created_at DESC
    LIMIT 5
");
$stmt->execute([$user['id']]);
$recent_tracking = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Logistics Maritime</title>
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

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
            color: var(--text-color);
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

        .card-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .card-label {
            color: var(--light-text);
            font-size: 0.9rem;
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

        .shipments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .shipments-table th,
        .shipments-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .shipments-table th {
            background-color: var(--secondary-color);
            font-weight: bold;
        }

        .shipments-table tr:last-child td {
            border-bottom: none;
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

        .tracking-timeline {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 30px;
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

        .contact-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .contact-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .contact-card:hover {
            transform: translateY(-5px);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: rgba(0, 102, 204, 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
        }

        .contact-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .contact-info {
            color: var(--light-text);
            margin-bottom: 15px;
        }

        .contact-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .contact-btn:hover {
            background-color: #0056b3;
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

            .dashboard-cards, .contact-cards {
                grid-template-columns: 1fr;
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
            <a href="../index.php" class="back-button" aria-label="Kembali ke Beranda">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
                <a href="user-dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="#" class="menu-item" onclick="showSection('tracking')">
                    <i class="fas fa-search-location"></i> Track Shipment
                </a>
                <a href="#" class="menu-item" onclick="showSection('shipments')">
                    <i class="fas fa-shipping-fast"></i> My Shipments
                </a>
                <a href="new-shipment.php" class="menu-item">
                    <i class="fas fa-plus-circle"></i> New Shipment
                </a>
                <a href="#" class="menu-item" onclick="showSection('contact')">
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
                <h1 class="page-title">Dashboard</h1>
                <div>
                    <a href="new-shipment.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> New Shipment
                    </a>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Total Shipments</h2>
                        <div class="card-icon bg-primary">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                    <div class="card-value"><?php echo count($shipments); ?></div>
                    <div class="card-label">All time shipments</div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">In Transit</h2>
                        <div class="card-icon bg-info">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                    </div>
                    <div class="card-value">
                        <?php 
                        $inTransit = array_filter($shipments, function($s) {
                            return $s['status'] === 'In Transit';
                        });
                        echo count($inTransit);
                        ?>
                    </div>
                    <div class="card-label">Shipments on the way</div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Delivered</h2>
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="card-value">
                        <?php 
                        $delivered = array_filter($shipments, function($s) {
                            return $s['status'] === 'Delivered';
                        });
                        echo count($delivered);
                        ?>
                    </div>
                    <div class="card-label">Completed shipments</div>
                </div>
            </div>

            <!-- Tracking Form Section -->
            <div id="tracking-section" class="section">
                <div class="tracking-form">
                    <h2 class="form-title">Track Your Shipment</h2>
                    <form id="tracking-form" action="track-shipment.php" method="GET">
                        <div class="form-group">
                            <input type="text" name="tracking_number" class="form-control" placeholder="Enter tracking number" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Track
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recent Tracking Updates -->
            <div class="tracking-timeline">
                <h2 class="section-title">Recent Tracking Updates</h2>
                <?php if (count($recent_tracking) > 0): ?>
                    <?php foreach ($recent_tracking as $tracking): ?>
                        <div class="timeline-item">
                            <div class="timeline-date"><?php echo date('Y-m-d H:i', strtotime($tracking['created_at'])); ?></div>
                            <div class="timeline-content">
                                <div class="timeline-title">
                                    Tracking #<?php echo htmlspecialchars($tracking['tracking_number']); ?>
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
                    <p>No recent tracking updates.</p>
                <?php endif; ?>
            </div>

            <!-- My Shipments Section -->
            <div id="shipments-section" class="section">
                <h2 class="section-title">My Shipments</h2>
                <?php if (count($shipments) > 0): ?>
                    <table class="shipments-table">
                        <thead>
                            <tr>
                                <th>Tracking Number</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shipments as $shipment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shipment['tracking_number']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['sender_address']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['receiver_address']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($shipment['status']); ?>">
                                            <?php echo htmlspecialchars($shipment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $shipment['item_count']; ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($shipment['created_at'])); ?></td>
                                    <td>
                                        <a href="view-shipment.php?id=<?php echo $shipment['id']; ?>" class="action-btn view-btn">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No shipments found.</p>
                <?php endif; ?>
            </div>

            <!-- Service Center Section -->
            <div id="contact-section" class="section">
                <h2 class="section-title">Contact Service Center</h2>
                <div class="contact-cards">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h3 class="contact-title">Call Center</h3>
                        <p class="contact-info">Available 24/7 for your inquiries</p>
                        <a href="tel:+621234567890" class="contact-btn">
                            <i class="fas fa-phone"></i> Call Now
                        </a>
                    </div>
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="contact-title">Email Support</h3>
                        <p class="contact-info">Get response within 24 hours</p>
                        <a href="mailto:support@logistikmaritim.com" class="contact-btn">
                            <i class="fas fa-envelope"></i> Send Email
                        </a>
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

        // Section navigation
        function showSection(sectionId) {
            // Hide all sections first
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => {
                section.style.display = 'none';
            });

            // Show the selected section
            document.getElementById(sectionId + '-section').style.display = 'block';

            // Update active menu item
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.classList.remove('active');
                if (item.textContent.toLowerCase().includes(sectionId)) {
                    item.classList.add('active');
                }
            });
        }

        // Live chat function (placeholder)
        function openLiveChat() {
            alert('Live chat feature will be implemented soon!');
        }
    </script>
</body>

</html>
