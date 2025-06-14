<?php
require_once 'config/database.php';
require_once 'config/session.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

// Validate token
if (empty($token)) {
    $error = 'Invalid or missing reset token';
} else {
    // Check if token exists and is valid
    $sql = "SELECT pr.*, u.username, u.email 
            FROM password_resets pr 
            JOIN users u ON pr.user_id = u.id 
            WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW() 
            LIMIT 1";
    $reset = get_row($sql, 's', [$token]);
    
    if (!$reset) {
        $error = 'Invalid or expired reset token. Please request a new password reset.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $token = $_POST['token'] ?? '';
    
    // Validate input
    if (empty($password) || empty($confirm_password)) {
        $error = 'Please enter both password fields';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if token exists and is valid
        $sql = "SELECT pr.*, u.id as user_id 
                FROM password_resets pr 
                JOIN users u ON pr.user_id = u.id 
                WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW() 
                LIMIT 1";
        $reset = get_row($sql, 's', [$token]);
        
        if (!$reset) {
            $error = 'Invalid or expired reset token. Please request a new password reset.';
        } else {
            // Update user password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            execute_query($sql, 'si', [$hashed_password, $reset['user_id']]);
            
            // Mark token as used
            $sql = "UPDATE password_resets SET used = 1, used_at = NOW() WHERE id = ?";
            execute_query($sql, 'i', [$reset['id']]);
            
            // Log activity
            $sql = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                    VALUES (?, 'password_reset', 'User reset password', ?, ?)";
            execute_query($sql, 'iss', [
                $reset['user_id'],
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);
            
            $success = 'Your password has been reset successfully. You can now login with your new password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Logistics Maritime</title>
    <link rel="stylesheet" href="logistikmaritim/login/login.css">
    <style>
        .reset-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .reset-header img {
            height: 60px;
            margin-bottom: 15px;
        }
        
        .reset-header h1 {
            font-size: 1.8em;
            color: #333;
            margin: 0;
        }
        
        .reset-form h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #333;
        }
        
        .password-requirements {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        
        .password-requirements h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
            color: #666;
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
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: #0066cc;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <img src="logistikmaritim/image/logo.png" alt="Logo">
            <h1>Logistics Maritime</h1>
        </div>
        
        <div class="reset-form">
            <h2>Reset Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <div class="login-link">
                    <a href="login.php">Kembali ke halaman login</a>
                </div>
            <?php elseif (empty($error) || $error !== 'Invalid or missing reset token' && $error !== 'Invalid or expired reset token. Please request a new password reset.'): ?>
                <div class="password-requirements">
                    <h4>Persyaratan Password:</h4>
                    <ul>
                        <li>Minimal 8 karakter</li>
                        <li>Kombinasi huruf dan angka</li>
                        <li>Setidaknya satu karakter khusus disarankan</li>
                    </ul>
                </div>
                
                <form method="POST" action="" novalidate>
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password baru" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi password baru" required>
                    </div>
                    
                    <button type="submit" class="btn-login">Reset Password</button>
                </form>
            <?php else: ?>
                <div class="login-link">
                    <a href="login.php">Kembali ke halaman login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
