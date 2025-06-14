<?php
// Security configuration

require_once 'environment.php';

class Security {
    private static $instance = null;
    private $config;
    
    private function __construct() {
        $this->config = [
            'password_hash_algo' => PASSWORD_ARGON2ID,
            'password_hash_options' => [
                'memory_cost' => 65536,
                'time_cost' => 4,
                'threads' => 3
            ],
            'token_length' => 32,
            'token_expiry' => 3600,
            'max_login_attempts' => 5,
            'lockout_time' => 900,
            'session_regenerate_time' => 300,
            'csrf_token_name' => 'csrf_token',
            'csrf_token_length' => 32,
            'allowed_file_types' => [
                'image/jpeg',
                'image/png',
                'image/gif',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
            'max_file_size' => 5242880, // 5MB
            'allowed_origins' => [
                'http://localhost',
                'http://localhost:8080',
                'https://yourdomain.com'
            ]
        ];
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function hashPassword($password) {
        return password_hash(
            $password,
            $this->config['password_hash_algo'],
            $this->config['password_hash_options']
        );
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function generateToken($length = null) {
        $length = $length ?? $this->config['token_length'];
        return bin2hex(random_bytes($length));
    }
    
    public function generateCSRFToken() {
        $token = $this->generateToken($this->config['csrf_token_length']);
        $_SESSION[$this->config['csrf_token_name']] = $token;
        return $token;
    }
    
    public function validateCSRFToken($token) {
        if (!isset($_SESSION[$this->config['csrf_token_name']])) {
            return false;
        }
        
        $valid = hash_equals($_SESSION[$this->config['csrf_token_name']], $token);
        unset($_SESSION[$this->config['csrf_token_name']]);
        return $valid;
    }
    
    public function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public function validateFile($file, $type = null, $size = null) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return false;
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        $type = $type ?? $this->config['allowed_file_types'];
        $size = $size ?? $this->config['max_file_size'];
        
        if (!in_array($file['type'], $type)) {
            return false;
        }
        
        if ($file['size'] > $size) {
            return false;
        }
        
        return true;
    }
    
    public function checkLoginAttempts($username) {
        $attempts = isset($_SESSION['login_attempts'][$username]) 
            ? $_SESSION['login_attempts'][$username] 
            : 0;
            
        if ($attempts >= $this->config['max_login_attempts']) {
            $lockout_time = isset($_SESSION['lockout_time'][$username]) 
                ? $_SESSION['lockout_time'][$username] 
                : 0;
                
            if (time() - $lockout_time < $this->config['lockout_time']) {
                return false;
            }
            
            unset($_SESSION['login_attempts'][$username]);
            unset($_SESSION['lockout_time'][$username]);
        }
        
        return true;
    }
    
    public function recordLoginAttempt($username, $success) {
        if ($success) {
            unset($_SESSION['login_attempts'][$username]);
            unset($_SESSION['lockout_time'][$username]);
            return true;
        }
        
        if (!isset($_SESSION['login_attempts'][$username])) {
            $_SESSION['login_attempts'][$username] = 0;
        }
        
        $_SESSION['login_attempts'][$username]++;
        
        if ($_SESSION['login_attempts'][$username] >= $this->config['max_login_attempts']) {
            $_SESSION['lockout_time'][$username] = time();
        }
        
        return false;
    }
    
    public function regenerateSession() {
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
            return true;
        }
        
        if (time() - $_SESSION['last_regeneration'] > $this->config['session_regenerate_time']) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
            return true;
        }
        
        return false;
    }
    
    public function validateOrigin($origin) {
        return in_array($origin, $this->config['allowed_origins']);
    }
    
    public function setSecurityHeaders() {
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\'; style-src \'self\' \'unsafe-inline\';');
        
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
} 