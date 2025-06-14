<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Redirect if already logged in
if (is_logged_in()) {
    // Ambil role dari session
    $role = $_SESSION['user']['role'] ?? '';

    // Arahkan sesuai role
    if ($role === 'admin') {
        header('Location: ../admin/dashboard.php');
    } elseif ($role === 'user') {
        header('Location: ../user/user-dashboard.php');
    } else {
        // Kalau role tidak dikenali, redirect ke halaman umum
        header('Location: ../index.php');
    }
    exit;
}


$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($full_name)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if username or email already exists
        $sql = "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1";
        $existing_user = get_row($sql, 'ss', [$username, $email]);

        if ($existing_user) {
            $error = 'Username or email already exists';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password,
                'full_name' => $full_name,
                'role' => 'user',
                'status' => 'active'
            ];

            $user_id = insert_data('users', $data);

            if ($user_id) {
                // Log activity
                $sql = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?, 'register', 'New user registration', ?, ?)";
                execute_query($sql, 'iss', [
                    $user_id,
                    $_SERVER['REMOTE_ADDR'],
                    $_SERVER['HTTP_USER_AGENT']
                ]);

                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
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
    <title>Register - Logistics Maritime</title>
    <link rel="stylesheet" href="../logistikmaritim/login/register.css">
</head>
<body>
    <div class="register-container">
        <!-- Left Side - Image/Info -->
        <div class="register-image-section">
            <a href="../index.php" class="back-button" aria-label="Kembali ke Beranda">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div class="image-overlay">
                <div class="image-content">
                    <h2>Bergabung dengan Kami!</h2>
                    <p>Daftarkan diri Anda dan nikmati layanan logistik maritim terbaik dengan teknologi terdepan.</p>
                    <div class="benefits-list">
                        <div class="benefit-item">
                            <span class="benefit-icon">🌊</span>
                            <div class="benefit-text">
                                <h4>Jaringan Luas</h4>
                                <p>10+ pelabuhan di seluruh Indonesia</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">⚡</span>
                            <div class="benefit-text">
                                <h4>Pengiriman Cepat</h4>
                                <p>Estimasi waktu yang akurat</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <span class="benefit-icon">🔐</span>
                            <div class="benefit-text">
                                <h4>Keamanan Terjamin</h4>
                                <p>Asuransi dan tracking real-time</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="register-form-section">
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

                <!-- Register Form -->
                <div class="register-form">
                    <h2>Buat Akun Baru</h2>
                    <p class="form-subtitle">Lengkapi informasi di bawah untuk membuat akun</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="auth-form" novalidate>
                        <div class="form-group">
                            <label for="full_name">Nama Lengkap</label>
                            <input type="text" id="full_name" name="full_name" placeholder="Nama lengkap" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Password" required minlength="8">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                        </div>
                        <button type="submit" class="btn-register">Daftar Sekarang</button>
                    </form>
                    <div class="login-link">
                        <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        if (this.value !== document.getElementById('password').value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    </script>
</body>
</html>