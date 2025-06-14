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
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email)) {
        $error = 'Username and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if username or email already exists (excluding current user)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $user['id']]);
        
        if ($stmt->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            // Update profile
            $updateFields = ['username = ?', 'email = ?'];
            $updateValues = [$username, $email];
            
            // If password change is requested
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = 'Current password is required to change password.';
                } elseif (!password_verify($current_password, $user['password'])) {
                    $error = 'Current password is incorrect.';
                } elseif (strlen($new_password) < 6) {
                    $error = 'New password must be at least 6 characters long.';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'New password and confirmation do not match.';
                } else {
                    $updateFields[] = 'password = ?';
                    $updateValues[] = password_hash($new_password, PASSWORD_DEFAULT);
                }
            }
            
            if (empty($error)) {
                $updateValues[] = $user['id'];
                $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute($updateValues)) {
                    // Update session data
                    $_SESSION['username'] = $username;
                    $_SESSION['name'] = $username;
                    $_SESSION['email'] = $email;
                    
                    $message = 'Profile updated successfully!';
                    
                    // Refresh user data
                    $user = getUserData();
                } else {
                    $error = 'Failed to update profile. Please try again.';
                }
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
    <title>My Profile - Logistics Maritime</title>
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
            padding-top: 80px;
        }

        /* Header */
        .navbar {
            background: white !important;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
            position: relative;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 20px;
            font-weight: bold;
            color: #0a3356 !important;
            text-decoration: none;
            font-size: 1.25rem;
        }

        .navbar-brand::before {
            content: '';
            width: 40px;
            height: 40px;
            background: url('../logistikmaritim/image/logo.png') center/contain no-repeat;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-toggler {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 0.5rem;
            background: none;
            border: none;
        }

        .navbar-toggler span,
        .navbar-toggler-icon {
            width: 25px;
            height: 3px;
            background: #0a3356;
            margin: 3px 0;
            transition: 0.3s;
            display: block;
        }

        .navbar-collapse {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .navbar-nav.me-auto {
            margin-left: 2rem;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            text-decoration: none;
            color: #333 !important;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .nav-link:hover, 
        .nav-link.active {
            color: #0a3356 !important;
            background-color: rgba(10, 51, 86, 0.1);
        }

        .dropdown {
            position: relative;
        }

        .dropdown-toggle::after {
            content: '▼';
            font-size: 0.8rem;
            margin-left: 0.5rem;
            transition: transform 0.3s;
        }

        .dropdown.show .dropdown-toggle::after {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 220px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1001;
            border: 1px solid #e0e0e0;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            padding: 0.8rem 1.2rem;
            color: #333 !important;
            text-decoration: none;
            transition: all 0.3s;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: #0a3356 !important;
            padding-left: 1.5rem;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #e0e0e0;
            border: none;
            margin: 0;
        }

        /* Main Content */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--text-color);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--light-text);
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .profile-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-role {
            font-size: 1rem;
            opacity: 0.9;
        }

        .profile-content {
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

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
            color: var(--text-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
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
            color: var(--light-text);
        }

        .input-group {
            position: relative;
        }

        .input-group-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
        }

        .input-group .form-control {
            padding-left: 45px;
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
            color: var(--text-color);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
            border-color: var(--light-text);
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--light-text);
            cursor: pointer;
            padding: 5px;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .form-text {
            font-size: 0.875rem;
            color: var(--light-text);
            margin-top: 5px;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .navbar-toggler {
                display: flex;
            }
            
            .navbar-collapse {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: white;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                opacity: 0;
                visibility: hidden;
                transform: translateY(-20px);
                transition: all 0.3s ease;
                flex-direction: column;
                padding: 1rem 0;
            }
            
            .navbar-collapse.show {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }
            
            .navbar-nav {
                flex-direction: column;
                gap: 0;
                width: 100%;
            }
            
            .navbar-nav.me-auto {
                margin-left: 0;
                margin-bottom: 1rem;
            }
            
            .nav-link {
                padding: 1rem 2rem;
                border-bottom: 1px solid #f0f0f0;
                border-radius: 0;
                width: 100%;
                justify-content: flex-start;
            }

            .container {
                padding: 15px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .profile-header {
                padding: 20px;
            }

            .profile-avatar {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }

            .profile-content {
                padding: 20px;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">My Profile</h1>
            <div class="breadcrumb">
                <a href="user-dashboard.php">Dashboard</a>
                <span>/</span>
                <span>Profile</span>
            </div>
        </div>

        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
                <div class="profile-role">Customer Account</div>
            </div>

            <!-- Profile Content -->
            <div class="profile-content">
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

                <form action="profile.php" method="POST">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-user-circle"></i> Basic Information
                        </h3>
                        
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <i class="fas fa-user input-group-icon"></i>
                                <input type="text" id="username" name="username" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <i class="fas fa-envelope input-group-icon"></i>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>

                    <!-- Password Change -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-lock"></i> Change Password
                        </h3>
                        <p class="form-text">Leave password fields empty if you don't want to change your password.</p>
                        
                        <div class="form-group">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <i class="fas fa-key input-group-icon"></i>
                                <input type="password" id="current_password" name="current_password" class="form-control">
                                <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock input-group-icon"></i>
                                <input type="password" id="new_password" name="new_password" class="form-control">
                                <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Password must be at least 6 characters long.</div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock input-group-icon"></i>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                        <a href="user-dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleNavbar() {
            const navbar = document.getElementById('navbarNav');
            const toggler = document.querySelector('.navbar-toggler');
            
            navbar.classList.toggle('show');
            toggler.classList.toggle('active');
        }

        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.target.closest('.dropdown');
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (menu !== dropdownMenu) {
                    menu.classList.remove('show');
                    menu.closest('.dropdown').classList.remove('show');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('show');
            dropdownMenu.classList.toggle('show');
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.parentNode.querySelector('.password-toggle i');
            
            if (field.type === 'password') {
                field.type = 'text';
                button.classList.remove('fa-eye');
                button.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                button.classList.remove('fa-eye-slash');
                button.classList.add('fa-eye');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                    menu.closest('.dropdown').classList.remove('show');
                });
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navbar = document.getElementById('navbarNav');
            const toggler = document.querySelector('.navbar-toggler');
            
            if (!event.target.closest('.navbar') && navbar.classList.contains('show')) {
                navbar.classList.remove('show');
                toggler.classList.remove('active');
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const currentPassword = document.getElementById('current_password').value;
            
            if (newPassword && !currentPassword) {
                e.preventDefault();
                alert('Current password is required to change password.');
                return;
            }
            
            if (newPassword && newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New password and confirmation do not match.');
                return;
            }
            
            if (newPassword && newPassword.length < 6) {
                e.preventDefault();
                alert('New password must be at least 6 characters long.');
                return;
            }
        });
    </script>
</body>

</html>
