<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';
$forgotPasswordError = '';
$forgotPasswordSuccess = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Get user from database
        $sql = "SELECT * FROM users WHERE username = ? AND status = 'active' LIMIT 1";
        $user = get_row($sql, 's', [$username]);

        if ($user && password_verify($password, $user['password'])) {
            // Set session data
            set_session_data('user_id', $user['id']);
            set_session_data('username', $user['username']);
            set_session_data('user_role', $user['role']);
            set_session_data('user_data', [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'role' => $user['role']
            ]);

            // Log activity
            $sql = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                    VALUES (?, 'login', 'User logged in', ?, ?)";
            execute_query($sql, 'iss', [
                $user['id'],
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: ../user/user-dashboard.php');
            }
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}

// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'forgot_password') {
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $forgotPasswordError = 'Semua field harus diisi';
    } elseif (strlen($new_password) < 6) {
        $forgotPasswordError = 'Password minimal 6 karakter';
    } elseif ($new_password !== $confirm_password) {
        $forgotPasswordError = 'Konfirmasi password tidak cocok';
    } else {
        // Check if email exists
        $sql = "SELECT id, username, email, full_name FROM users WHERE email = ? AND status = 'active' LIMIT 1";
        $user = get_row($sql, 's', [$email]);
        
        if ($user) {
            // Update password directly
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            execute_query($sql, 'si', [$hashed_password, $user['id']]);
            
            // Log activity
            $sql = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                    VALUES (?, 'password_reset', 'User reset password via forgot password', ?, ?)";
            execute_query($sql, 'iss', [
                $user['id'],
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);
            
            $forgotPasswordSuccess = "Password berhasil diubah! Silakan login dengan password baru Anda.";
        } else {
            $forgotPasswordError = "Email tidak ditemukan dalam sistem";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Logistics Maritime</title>
    <link rel="stylesheet" href="../logistikmaritim/login/login.css">
    <style>
        /* Additional styles for forgot password */
        .forgot-password-link {
            text-align: right;
            margin-top: -15px;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
        
        .forgot-password-link a {
            color: #0066cc;
            text-decoration: none;
        }
        
        .forgot-password-link a:hover {
            text-decoration: underline;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 450px;
            max-width: 90%;
            position: relative;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
        }
        
        .close-modal:hover {
            color: #000;
        }
        
        .modal h3 {
            margin-top: 0;
            color: #333;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        
        .modal p {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95em;
        }
        
        .alert {
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        
        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .password-requirements {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .password-requirements h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
            font-size: 1em;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
            color: #666;
            font-size: 0.9em;
        }
        
        .password-requirements li {
            margin-bottom: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }
        
        .btn-reset {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-reset:hover {
            background-color: #0056b3;
        }
        
        .btn-reset:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Image/Info -->
        <div class="login-image-section">
            <a href="../index.php" class="back-button" aria-label="Kembali ke Beranda">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div class="image-overlay">
                <div class="image-content">
                    <h2>Selamat Datang Kembali!</h2>
                    <p>Masuk untuk mengelola pengiriman Anda dan nikmati layanan logistik maritim terbaik.</p>
                    <div class="benefits-list">
                        <div class="benefit-item">
                            <span class="benefit-icon">🚢</span>
                            <div class="benefit-text">
                                <h4>Pelacakan Real-Time</h4>
                                <p>Lacak status pengiriman kapan saja</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">🔒</span>
                            <div class="benefit-text">
                                <h4>Akun Aman</h4>
                                <p>Keamanan data dan privasi terjamin</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">⚡</span>
                            <div class="benefit-text">
                                <h4>Respon Cepat</h4>
                                <p>Dukungan pelanggan 24/7</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-section">
            <div class="form-container">
                <!-- Logo -->
                <div class="logo-section">
                    <div class="logo">
                        <div class="logo-icon">
                            <img src="../logistikmaritim/image/logo.png" alt="Logo" height="50">
                        </div>
                        <div class="logo-text">
                            <h1>Logistics Maritime</h1>
                        </div>
                    </div>
                </div>

                <!-- Login Form -->
                <div class="login-form">
                    <h2>Masuk ke Akun Anda</h2>
                    <p class="form-subtitle">Silakan login untuk melanjutkan</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="auth-form" novalidate>
                        <input type="hidden" name="action" value="login">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="forgot-password-link">
                            <a href="#" id="forgotPasswordLink">Lupa password?</a>
                        </div>
                        <button type="submit" class="btn-login">Login</button>
                    </form>
                    <div class="register-link">
                        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Reset Password</h3>
            <p>Masukkan email Anda dan password baru untuk mereset password akun Anda.</p>
            
            <?php if ($forgotPasswordError): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($forgotPasswordError); ?></div>
            <?php endif; ?>
            
            <?php if ($forgotPasswordSuccess): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($forgotPasswordSuccess); ?></div>
            <?php endif; ?>
            
            <div class="password-requirements">
                <h4>Persyaratan Password:</h4>
                <ul>
                    <li>Minimal 8 karakter</li>
                    <li>Gunakan kombinasi huruf dan angka</li>
                    <li>Hindari password yang mudah ditebak</li>
                </ul>
            </div>
            
            <form method="POST" action="" novalidate>
                <input type="hidden" name="action" value="forgot_password">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan email terdaftar" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Masukkan password baru" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi password baru" required>
                </div>
                <button type="submit" class="btn-reset">Reset Password</button>
            </form>
        </div>
    </div>
    
    <script>
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('forgotPasswordModal');
            const forgotPasswordLink = document.getElementById('forgotPasswordLink');
            const closeModal = document.querySelector('.close-modal');
            
            // Open modal
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                modal.style.display = 'block';
            });
            
            // Close modal when clicking X
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Show modal if there was a forgot password submission
            <?php if ($forgotPasswordError || $forgotPasswordSuccess): ?>
                modal.style.display = 'block';
            <?php endif; ?>
            
            // Password confirmation validation
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            
            function validatePassword() {
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Password tidak cocok');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
            
            newPassword.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);
        });
    </script>
</body>
</html>
