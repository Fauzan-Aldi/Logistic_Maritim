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
    header('Location: user-dashboard.php');
    exit();
}

// Get shipment details - ensure user can only see their own shipments
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
    header('Location: user-dashboard.php');
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
    <title>Detail Pengiriman - Logistics Maritime</title>
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

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        .nav-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-menu a:hover {
            color: var(--primary-color);
        }

        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-logout {
            padding: 8px 16px;
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background-color: #e9ecef;
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }

        .page-header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--dark-color);
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

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
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
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
        }

        .shipment-details {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .detail-item {
            margin-bottom: 15px;
        }

        .detail-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
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

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin: 30px 0 15px;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .items-table th,
        .items-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .items-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .tracking-timeline {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .timeline-item {
            position: relative;
            padding: 20px 0 20px 30px;
            border-left: 2px solid #ddd;
        }

        .timeline-item:last-child {
            border-left: 2px solid transparent;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 20px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 2px solid white;
        }

        .timeline-date {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 5px;
        }

        .timeline-status {
            margin-bottom: 5px;
        }

        .timeline-location {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .timeline-description {
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #666;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-shipping-fast"></i>
                    Detail Pengiriman
                </h1>
                <div class="breadcrumb">
                    <a href="user-dashboard.php">Dashboard</a>
                    <span>/</span>
                    <span>Detail Pengiriman</span>
                </div>
            </div>
            <div class="action-buttons">
                <a href="user-dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                <a href="track-shipment.php?tracking=<?php echo urlencode($shipment['tracking_number']); ?>" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Lacak Pengiriman
                </a>
            </div>
        </div>

        <!-- Shipment Details -->
        <div class="shipment-details">
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Nomor Tracking</div>
                    <div class="detail-value"><?php echo htmlspecialchars($shipment['tracking_number']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="status-badge status-<?php echo strtolower($shipment['status']); ?>">
                            <?php echo htmlspecialchars($shipment['status']); ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Asal</div>
                    <div class="detail-value"><?php echo htmlspecialchars($shipment['sender_address']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tujuan</div>
                    <div class="detail-value"><?php echo htmlspecialchars($shipment['receiver_address']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tanggal Dibuat</div>
                    <div class="detail-value"><?php echo date('d M Y, H:i', strtotime($shipment['created_at'])); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Terakhir Diperbarui</div>
                    <div class="detail-value"><?php echo date('d M Y, H:i', strtotime($shipment['updated_at'])); ?></div>
                </div>
            </div>
        </div>

        <!-- Shipment Items -->
        <h2 class="section-title">
            <i class="fas fa-box"></i>
            Daftar Item (<?php echo count($items); ?>)
        </h2>

        <?php if (count($items) > 0): ?>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Jumlah</th>
                        <th>Berat (kg)</th>
                        <th>Deskripsi</th>
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
        <?php else: ?>
            <div class="empty-state">
                <p>Tidak ada item yang tercatat untuk pengiriman ini.</p>
            </div>
        <?php endif; ?>

        <!-- Tracking History -->
        <h2 class="section-title">
            <i class="fas fa-history"></i>
            Riwayat Pelacakan
        </h2>

        <div class="tracking-timeline">
            <?php if (count($tracking_history) > 0): ?>
                <?php foreach ($tracking_history as $tracking): ?>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo date('d M Y, H:i', strtotime($tracking['created_at'])); ?>
                        </div>
                        <div class="timeline-status">
                            <span class="status-badge status-<?php echo strtolower($tracking['status']); ?>">
                                <?php echo htmlspecialchars($tracking['status']); ?>
                            </span>
                        </div>
                        <div class="timeline-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($tracking['location']); ?>
                        </div>
                        <div class="timeline-description">
                            <?php echo htmlspecialchars($tracking['description']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>Belum ada riwayat pelacakan untuk pengiriman ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
