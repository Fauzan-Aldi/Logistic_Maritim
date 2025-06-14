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
$message = '';
$error = '';
$shipment = null;

// Get shipment ID
$shipment_id = $_GET['id'] ?? null;

if (!$shipment_id) {
    header('Location: dashboard.php');
    exit;
}

// Fetch shipment details
$stmt = $pdo->prepare("
    SELECT s.*, u.username as user_name, u.email as user_email 
    FROM shipments s 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE s.id = ?
");
$stmt->execute([$shipment_id]);
$shipment = $stmt->fetch();

if (!$shipment) {
    header('Location: dashboard.php');
    exit;
}

// Check if current user has permission to view this shipment
if ($user['role'] !== 'admin' && $shipment['user_id'] != $user['id']) {
    // Redirect if not admin and not the shipment owner
    header('Location: dashboard.php');
    exit;
}

// Fetch all users for dropdown
$stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE role = 'user' ORDER BY username");
$stmt->execute();
$users = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $tracking_number = trim($_POST['tracking_number'] ?? '');
    $sender_address = trim($_POST['sender_address'] ?? '');
    $receiver_address = trim($_POST['receiver_address'] ?? '');
    $status = $_POST['status'] ?? '';
    
    // Validation
    if (empty($user_id) || empty($tracking_number) || empty($sender_address) || empty($receiver_address) || empty($status)) {
        $error = 'All fields are required.';
    } else {
        // Check if tracking number exists for other shipments
        $stmt = $pdo->prepare("SELECT id FROM shipments WHERE tracking_number = ? AND id != ?");
        $stmt->execute([$tracking_number, $shipment_id]);
        
        if ($stmt->fetch()) {
            $error = 'Tracking number already exists.';
        } else {
            // Update shipment
            $stmt = $pdo->prepare("
                UPDATE shipments 
                SET user_id = ?, tracking_number = ?, sender_address = ?, receiver_address = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            if ($stmt->execute([$user_id, $tracking_number, $sender_address, $receiver_address, $status, $shipment_id])) {
                // Add tracking update
                $stmt = $pdo->prepare("
                    INSERT INTO shipment_tracking (shipment_id, status, location, description, created_at)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$shipment_id, $status, $receiver_address, "Status updated by admin: " . $status]);
                
                $message = 'Shipment updated successfully!';
                
                // Refresh shipment data
                $stmt = $pdo->prepare("
                    SELECT s.*, u.username as user_name, u.email as user_email 
                    FROM shipments s 
                    LEFT JOIN users u ON s.user_id = u.id 
                    WHERE s.id = ?
                ");
                $stmt->execute([$shipment_id]);
                $shipment = $stmt->fetch();
            } else {
                $error = 'Failed to update shipment. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Shipment - Admin Dashboard</title>
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
            max-width: 800px;
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

        .form-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-color), var(--info-color));
            color: white;
            padding: 25px;
            text-align: center;
        }

        .form-content {
            padding: 30px;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .form-control:disabled {
            background-color: var(--secondary-color);
            color: #666;
        }

        select.form-control {
            cursor: pointer;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
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

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: var(--dark-color);
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .current-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid var(--info-color);
        }

        .current-info h4 {
            color: var(--info-color);
            margin-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
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

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .form-content {
                padding: 20px;
            }

            .btn-group {
                flex-direction: column;
            }

            .info-grid {
                grid-template-columns: 1fr;
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
                    <span>Welcome, <?php echo htmlspecialchars($user['username']); ?></span>
                    <a href="../login/logout.php" class="btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-edit"></i>
                Edit Shipment
            </h1>
            <div class="breadcrumb">
                <a href="dashboard.php">Dashboard</a>
                <span>/</span>
                <a href="dashboard.php?view=shipments">Shipments</a>
                <span>/</span>
                <span>Edit</span>
            </div>
        </div>

        <!-- Current Shipment Info -->
        <div class="current-info">
            <h4><i class="fas fa-info-circle"></i> Current Shipment Information</h4>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Tracking Number:</span>
                    <span><?php echo htmlspecialchars($shipment['tracking_number']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Current Status:</span>
                    <span class="status-badge status-<?php echo strtolower($shipment['status']); ?>">
                        <?php echo htmlspecialchars($shipment['status']); ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Customer:</span>
                    <span><?php echo htmlspecialchars($shipment['user_name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Created:</span>
                    <span><?php echo date('M j, Y H:i', strtotime($shipment['created_at'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="form-container">
            <div class="form-header">
                <h2><i class="fas fa-shipping-fast"></i> Update Shipment Details</h2>
                <p>Modify the shipment information below</p>
            </div>

            <div class="form-content">
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label for="user_id" class="form-label">
                            <i class="fas fa-user"></i> Customer
                        </label>
                        <select id="user_id" name="user_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo $u['id'] == $shipment['user_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($u['username'] . ' (' . $u['email'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tracking_number" class="form-label">
                            <i class="fas fa-barcode"></i> Tracking Number
                        </label>
                        <input type="text" id="tracking_number" name="tracking_number" class="form-control" 
                               value="<?php echo htmlspecialchars($shipment['tracking_number']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="sender_address" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Origin
                        </label>
                        <input type="text" id="sender_address" name="sender_address" class="form-control" 
                               value="<?php echo htmlspecialchars($shipment['sender_address']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="receiver_address" class="form-label">
                            <i class="fas fa-flag-checkered"></i> Destination
                        </label>
                        <input type="text" id="receiver_address" name="receiver_address" class="form-control" 
                               value="<?php echo htmlspecialchars($shipment['receiver_address']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">
                            <i class="fas fa-info-circle"></i> Status
                        </label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="Pending" <?php echo $shipment['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="In Transit" <?php echo $shipment['status'] === 'In Transit' ? 'selected' : ''; ?>>In Transit</option>
                            <option value="Delivered" <?php echo $shipment['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Cancelled" <?php echo $shipment['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Shipment
                        </button>
                        <a href="dashboard.php?view=shipments" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Shipments
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
