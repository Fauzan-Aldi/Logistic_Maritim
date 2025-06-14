<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Add this line to initialize $pdo
$pdo = Database::getInstance()->getConnection();

// Require login to access
requireLogin();

$user = getUserData();
$error = '';
$success = '';

// Fetch all users for admin to select from
$users = [];
if ($user['role'] === 'admin') {
    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE role = 'user' ORDER BY username");
    $stmt->execute();
    $users = $stmt->fetchAll();
}

// PASTE KODE INI UNTUK MENGGANTIKAN BLOK DI ATAS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. SESUAIKAN NAMA VARIABEL DENGAN FORM HTML
    $sender_address = $_POST['sender_address'] ?? '';
    $receiver_address = $_POST['receiver_address'] ?? '';
    $items = $_POST['items'] ?? [];
    
    // Get user_id - if admin, use selected user, otherwise use current user
    $shipment_user_id = $user['id'];
    if ($user['role'] === 'admin' && isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        $shipment_user_id = $_POST['user_id'];
    }

    // 2. GUNAKAN VARIABEL BARU UNTUK VALIDASI
    if (empty($sender_address) || empty($receiver_address) || empty($items)) {
        $error = 'Please fill in all required fields';
    } else {
        try {
            $pdo->beginTransaction();

            // Generate tracking number
            $tracking_number = 'TRK' . date('Ymd') . strtoupper(substr(uniqid(), -6));

            // 3. PERBAIKI QUERY INSERT AGAR SESUAI DENGAN DATABASE
            // Ubah query INSERT untuk shipments agar sesuai dengan struktur tabel di database
            // Ganti bagian ini:
            // Dengan ini (menggunakan sender_address dan receiver_address sebagai nama kolom):
            $stmt = $pdo->prepare(
                "INSERT INTO shipments (user_id, tracking_number, sender_address, receiver_address, status, created_at) VALUES (?, ?, ?, ?, 'Pending', NOW())"
            );
            // 4. GUNAKAN VARIABEL BARU SAAT EXECUTE
            $stmt->execute([$shipment_user_id, $tracking_number, $sender_address, $receiver_address]);
            $shipment_id = $pdo->lastInsertId();

            // Insert shipment details (ini sudah benar)
            $stmt = $pdo->prepare("INSERT INTO shipment_details (shipment_id, item_name, quantity, weight, description) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                if (!empty($item['name'])) {
                    $stmt->execute([
                        $shipment_id,
                        $item['name'],
                        $item['quantity'] ?? 1,
                        $item['weight'] ?? 0,
                        $item['description'] ?? ''
                    ]);
                }
            }

            // 5. PERBAIKI JUGA QUERY TRACKING
            // Juga perlu menyesuaikan query untuk tracking:
            // Ganti bagian ini:
            // Dengan ini (menggunakan sender_address sebagai lokasi):
            $stmt = $pdo->prepare("INSERT INTO shipment_tracking (shipment_id, status, location, description, created_at) VALUES (?, 'Pending', ?, 'Shipment created', NOW())");
            $stmt->execute([$shipment_id, $sender_address]);

            $pdo->commit();
            $success = 'Shipment created successfully! Tracking Number: ' . $tracking_number;
        } catch (PDOException $e) {
            $pdo->rollBack();
            // Tampilkan error database yang sebenarnya saat debugging
            $error = 'Failed to create shipment. Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Shipment - Logistics Maritime</title>
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

        .new-shipment-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--dark-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .items-container {
            margin-top: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            background: var(--secondary-color);
        }

        .items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .items-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--dark-color);
        }

        .item-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 2fr auto;
            gap: 15px;
            margin-bottom: 15px;
            align-items: start;
        }

        .item-row-header {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
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

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-sm {
            padding: 8px 15px;
            font-size: 0.9rem;
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

        .section-divider {
            margin: 30px 0;
            border-top: 1px solid #e0e0e0;
        }

        .form-actions {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .user-select-container {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .user-select-title {
            font-weight: 600;
            color: #0891b2;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .new-shipment-container {
                padding: 15px;
            }

            .item-row {
                grid-template-columns: 1fr;
                gap: 10px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e0e0e0;
                margin-bottom: 15px;
            }

            .item-row-header {
                display: none;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>


    <div class="new-shipment-container">
        <h1 class="page-title">
            <i class="fas fa-plus-circle"></i>
            Create New Shipment
        </h1>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
                <br><br>
                <a href="dashboard.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Return to Dashboard
                </a>
            </div>
        <?php else: ?>
            <form method="POST" id="shipmentForm">
                <?php if ($user['role'] === 'admin'): ?>
                <div class="user-select-container">
                    <div class="user-select-title">
                        <i class="fas fa-user-tag"></i> Creating Shipment For:
                    </div>
                    <div class="form-group">
                        <select name="user_id" class="form-control" required>
                            <option value="">-- Select Customer --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>">
                                    <?php echo htmlspecialchars($u['username'] . ' (' . $u['email'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="sender_address" class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Origin
                    </label>
                    <input type="text" id="sender_address" name="sender_address" class="form-control" 
                           placeholder="Enter pickup location" required>
                </div>
                
                <div class="form-group">
                    <label for="receiver_address" class="form-label">
                        <i class="fas fa-flag-checkered"></i> Destination
                    </label>
                    <input type="text" id="receiver_address" name="receiver_address" class="form-control" 
                           placeholder="Enter delivery location" required>
                </div>

                <div class="items-container">
                    <div class="items-header">
                        <h3 class="items-title">
                            <i class="fas fa-box"></i> Shipment Items
                        </h3>
                        <button type="button" class="btn btn-success btn-sm" onclick="addItem()">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                    
                    <div class="item-row item-row-header">
                        <div>Item Name</div>
                        <div>Quantity</div>
                        <div>Weight (kg)</div>
                        <div>Description</div>
                        <div></div>
                    </div>
                    
                    <div id="itemsList">
                        <div class="item-row">
                            <input type="text" name="items[0][name]" class="form-control" placeholder="Item Name" required>
                            <input type="number" name="items[0][quantity]" class="form-control" placeholder="Quantity" value="1" min="1">
                            <input type="number" name="items[0][weight]" class="form-control" placeholder="Weight" step="0.01" min="0">
                            <input type="text" name="items[0][description]" class="form-control" placeholder="Description">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)" style="display: none;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Shipment
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        let itemCount = 1;

        function addItem() {
            const itemsList = document.getElementById('itemsList');
            const newItem = document.createElement('div');
            newItem.className = 'item-row';
            newItem.innerHTML = `
                <input type="text" name="items[${itemCount}][name]" class="form-control" placeholder="Item Name" required>
                <input type="number" name="items[${itemCount}][quantity]" class="form-control" placeholder="Quantity" value="1" min="1">
                <input type="number" name="items[${itemCount}][weight]" class="form-control" placeholder="Weight" step="0.01" min="0">
                <input type="text" name="items[${itemCount}][description]" class="form-control" placeholder="Description">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            itemsList.appendChild(newItem);
            itemCount++;

            // Show remove button for first item if there's more than one item
            if (itemCount > 1) {
                document.querySelector('.item-row:first-child .btn-danger').style.display = 'inline-flex';
            }
        }

        function removeItem(button) {
            const itemRow = button.parentElement;
            itemRow.remove();
            itemCount--;

            // Hide remove button for first item if it's the only item
            if (itemCount === 1) {
                document.querySelector('.item-row:first-child .btn-danger').style.display = 'none';
            }
        }
    </script>
</body>

</html>
