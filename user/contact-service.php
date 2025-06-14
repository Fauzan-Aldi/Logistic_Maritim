<?php
require_once '../config/database.php';
require_once '../config/session.php';

$pdo = Database::getInstance()->getConnection();

// Require login to access
requireLogin();

$user = getUserData();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $message_text = $_POST['message'] ?? '';
    
    if (empty($subject) || empty($message_text)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Insert into support_tickets table
        $stmt = $pdo->prepare("
            INSERT INTO support_tickets (user_id, subject, message, status, created_at)
            VALUES (?, ?, ?, 'Open', NOW())
        ");
        
        if ($stmt->execute([$user['id'], $subject, $message_text])) {
            $message = 'Your message has been sent successfully. Our team will contact you soon.';
        } else {
            $error = 'Failed to send message. Please try again later.';
        }
    }
}

// Get user's support tickets
$stmt = $pdo->prepare("
    SELECT * FROM support_tickets 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$user['id']]);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Service Center - Logistics Maritime</title>
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

        .contact-options {
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

        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .contact-form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 1rem;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
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

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .tickets-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .tickets-table th,
        .tickets-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .tickets-table th {
            background-color: var(--secondary-color);
            font-weight: bold;
        }

        .tickets-table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-open {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-in-progress {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-resolved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-closed {
            background-color: #e2e3e5;
            color: #383d41;
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

            .contact-options {
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
                <a href="track-shipment.php" class="menu-item">
                    <i class="fas fa-search-location"></i> Track Shipment
                </a>
                <a href="user-dashboard.php#shipments-section" class="menu-item">
                    <i class="fas fa-shipping-fast"></i> My Shipments
                </a>
                <a href="contact-service.php" class="menu-item active">
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
                <h1 class="page-title">Contact Service Center</h1>
                <div></div> <!-- Empty div for flex spacing -->
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Contact Options -->
            <div class="contact-options">
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