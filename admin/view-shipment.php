<?php
require_once '../config/database.php';
require_once '../config/session.php';

$pdo = Database::getInstance()->getConnection();

// Require login to access
requireLogin();

$user = getUserData();
$error = '';

// Get shipment ID from URL
$shipment_id = $_GET['id'] ?? null;

if (!$shipment_id) {
    header('Location: dashboard.php');
    exit();
}

// Get shipment details
$stmt = $pdo->prepare("
    SELECT s.*, COUNT(sd.id) as item_count 
    FROM shipments s 
    LEFT JOIN shipment_details sd ON s.id = sd.shipment_id 
    WHERE s.id = ? AND s.user_id = ? 
    GROUP BY s.id
");
$stmt->execute([$shipment_id, $user['id']]);
$shipment = $stmt->fetch();

if (!$shipment) {
    header('Location: dashboard.php');
    exit();
}

// Get shipment items
$stmt = $pdo->prepare("SELECT * FROM shipment_details WHERE shipment_id = ?");
$stmt->execute([$shipment_id]);
$items = $stmt->fetchAll();

// Get tracking history
$stmt = $pdo->prepare("SELECT * FROM shipment_tracking WHERE shipment_id = ? ORDER BY created_at DESC");
$stmt->execute([$shipment_id]);
$tracking_history = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Shipment - Logistics Maritime</title>
    <link rel="stylesheet" href="../logistikmaritim/css/index.css">
    <style>
        .view-shipment-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }

        .shipment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }

        .status-pending {
            background: #ffd700;
            color: #000;
        }

        .status-in-transit {
            background: #007bff;
            color: white;
        }

        .status-delivered {
            background: #28a745;
            color: white;
        }

        .status-cancelled {
            background: #dc3545;
            color: white;
        }

        .shipment-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
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

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .tracking-timeline {
            margin-top: 20px;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #ddd;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: -4px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #007bff;
        }

        .timeline-date {
            font-size: 0.9em;
            color: #666;
        }

        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
        }

        .edit-btn {
            background: #ffc107;
            color: #000;
        }

        .back-btn {
            background: #6c757d;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon">
                    <img src="logistikmaritim/image/logo.png" alt="logo" />
                </div>
                <span>Logistik Maritime</span>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="dashboard.php">Dashboard</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <div class="user-menu">
                    <span>Welcome, <?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'Admin'; ?></span>
                    <a href="../login/logout.php" class="btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="view-shipment-container">
        <div class="shipment-header">
            <h2>Shipment Details</h2>
            <div>
                <a href="edit-shipment.php?id=<?php echo $shipment['id']; ?>" class="action-btn edit-btn">Edit Shipment</a>
                <a href="dashboard.php" class="action-btn back-btn">Back to Dashboard</a>
            </div>
        </div>

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

        <h3>Items (<?php echo count($items); ?>)</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Weight (kg)</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['weight']); ?></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Tracking History</h3>
        <div class="tracking-timeline">
            <?php foreach ($tracking_history as $tracking): ?>
                <div class="timeline-item">
                    <div class="timeline-date"><?php echo date('Y-m-d H:i', strtotime($tracking['created_at'])); ?></div>
                    <div class="timeline-status">
                        <span class="status-badge status-<?php echo strtolower($tracking['status']); ?>">
                            <?php echo htmlspecialchars($tracking['status']); ?>
                        </span>
                    </div>
                    <div class="timeline-location"><?php echo htmlspecialchars($tracking['location']); ?></div>
                    <div class="timeline-description"><?php echo htmlspecialchars($tracking['description']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>