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
$confirm = $_GET['confirm'] ?? null;

if (!$user_id) {
    $_SESSION['error'] = 'User ID is required.';
    header('Location: dashboard.php?view=users');
    exit;
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = 'User not found.';
    header('Location: dashboard.php?view=users');
    exit;
}

// Prevent deleting admin users (except self-deletion with special confirmation)
if ($user['role'] === 'admin' && $user_id != $admin_user['id']) {
    $_SESSION['error'] = 'Cannot delete other admin users.';
    header('Location: dashboard.php?view=users');
    exit;
}

// Handle deletion confirmation
if ($confirm === 'yes' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Delete user's shipment tracking records
        $stmt = $pdo->prepare("
            DELETE st FROM shipment_tracking st 
            JOIN shipments s ON st.shipment_id = s.id 
            WHERE s.user_id = ?
        ");
        $stmt->execute([$user_id]);
        
        // Delete user's shipment details
        $stmt = $pdo->prepare("
            DELETE sd FROM shipment_details sd 
            JOIN shipments s ON sd.shipment_id = s.id 
            WHERE s.user_id = ?
        ");
        $stmt->execute([$user_id]);
        
        // Delete user's shipments
        $stmt = $pdo->prepare("DELETE FROM shipments WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete user's contact messages
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE email = ?");
        $stmt->execute([$user['email']]);
        
        // Delete user's general messages
        $stmt = $pdo->prepare("DELETE FROM general_messages WHERE email = ?");
        $stmt->execute([$user['email']]);
        
        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        
        $_SESSION['success'] = 'User "' . $user['username'] . '" has been deleted successfully.';
        
        // If admin deleted themselves, logout
        if ($user_id == $admin_user['id']) {
            header('Location: ../login/logout.php');
            exit;
        }
        
        header('Location: dashboard.php?view=users');
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Failed to delete user. Error: ' . $e->getMessage();
        header('Location: dashboard.php?view=users');
        exit;
    }
}

// Count user's data
$stmt = $pdo->prepare("SELECT COUNT(*) as shipment_count FROM shipments WHERE user_id = ?");
$stmt->execute([$user_id]);
$shipment_count = $stmt->fetchColumn();

$ticket_count = 0; // Set to 0 since table doesn't exist

$stmt = $pdo->prepare("SELECT COUNT(*) as message_count FROM contact_messages WHERE email = ?");
$stmt->execute([$user['email']]);
$message_count = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User - Admin dashboard.php</title>
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
            color: var(--danger-color);
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

        .warning-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 2px solid var(--danger-color);
        }

        .warning-header {
            background: linear-gradient(135deg, var(--danger-color), #c82333);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .warning-content {
            padding: 30px;
        }

        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid var(--info-color);
        }

        .user-info h4 {
            color: var(--info-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .role-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .role-admin {
            background: #e7f3ff;
            color: #0066cc;
        }

        .role-user {
            background: #f0f9ff;
            color: #0891b2;
        }

        .data-summary {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .data-summary h4 {
            color: #856404;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .data-list {
            list-style: none;
            padding: 0;
        }

        .data-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .data-list li:last-child {
            border-bottom: none;
        }

        .count-badge {
            background: var(--warning-color);
            color: #000;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .danger-warning {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .danger-warning h4 {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
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

        .confirmation-form {
            background: #fff;
            border: 2px solid var(--danger-color);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .checkbox-group {
            margin: 20px 0;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-weight: 600;
            color: var(--danger-color);
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .warning-content {
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
                <i class="fas fa-user-times"></i>
                Delete User
            </h1>
            <div class="breadcrumb">
                <a href="dashboard.php">dashboard.php</a>
                <span>/</span>
                <a href="dashboard.php?view=users">Users</a>
                <span>/</span>
                <span>Delete <?php echo htmlspecialchars($user['username']); ?></span>
            </div>
        </div>

        <!-- Warning Container -->
        <div class="warning-container">
            <div class="warning-header">
                <h2><i class="fas fa-exclamation-triangle"></i> DANGER ZONE</h2>
                <p>You are about to permanently delete a user account</p>
            </div>

            <div class="warning-content">
                <!-- User Information -->
                <div class="user-info">
                    <h4><i class="fas fa-user"></i> User Information</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Username:</span>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Role:</span>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Member Since:</span>
                            <span><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Data Summary -->
                <div class="data-summary">
                    <h4><i class="fas fa-database"></i> Data That Will Be Deleted</h4>
                    <ul class="data-list">
                        <li>
                            <span>User Account</span>
                            <span class="count-badge">1</span>
                        </li>
                        <li>
                            <span>Shipments</span>
                            <span class="count-badge"><?php echo $shipment_count; ?></span>
                        </li>
                        <li>
                            <span>Contact Messages</span>
                            <span class="count-badge"><?php echo $message_count; ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Danger Warning -->
                <div class="danger-warning">
                    <h4><i class="fas fa-exclamation-triangle"></i> Warning</h4>
                    <p><strong>This action cannot be undone!</strong> All user data including shipments, tracking information, and messages will be permanently deleted from the database.</p>
                    <?php if ($user['role'] === 'admin'): ?>
                        <p><strong>Additional Warning:</strong> You are about to delete an admin account. This will remove all administrative privileges associated with this user.</p>
                    <?php endif; ?>
                </div>

                <!-- Confirmation Form -->
                <div class="confirmation-form">
                    <h4><i class="fas fa-check-square"></i> Confirmation Required</h4>
                    <form method="POST" action="?id=<?php echo $user_id; ?>&confirm=yes" id="deleteForm">
                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" id="confirmDelete" required>
                                I understand that this action is permanent and cannot be undone
                            </label>
                        </div>
                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" id="confirmData" required>
                                I understand that all user data will be permanently deleted
                            </label>
                        </div>
                        <?php if ($user['role'] === 'admin'): ?>
                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" id="confirmAdmin" required>
                                I understand that I am deleting an admin account
                            </label>
                        </div>
                        <?php endif; ?>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-danger" id="deleteBtn" disabled>
                                <i class="fas fa-trash"></i> Delete User Permanently
                            </button>
                            <a href="view-user.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-eye"></i> View User
                            </a>
                            <a href="dashboard.php?view=users" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enable delete button only when all checkboxes are checked
        function checkFormValidity() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const deleteBtn = document.getElementById('deleteBtn');
            
            let allChecked = true;
            checkboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    allChecked = false;
                }
            });
            
            deleteBtn.disabled = !allChecked;
        }

        // Add event listeners to all checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', checkFormValidity);
        });

        // Final confirmation before submission
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            const username = '<?php echo addslashes($user['username']); ?>';
            const confirmMessage = `Are you absolutely sure you want to delete user "${username}"?\n\nThis action cannot be undone and will permanently delete:\n- User account\n- ${<?php echo $shipment_count; ?>} shipments\n- ${<?php echo $message_count; ?>} contact messages\n\nType "DELETE" to confirm:`;
            
            const userInput = prompt(confirmMessage);
            if (userInput !== 'DELETE') {
                e.preventDefault();
                alert('Deletion cancelled. You must type "DELETE" exactly to confirm.');
            }
        });
    </script>
</body>
</html>
