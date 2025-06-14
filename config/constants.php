<?php
// Application constants and settings

// Application information
define('APP_NAME', 'Logistik Maritim');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/php-native');
define('APP_ROOT', dirname(__DIR__));

// File paths
define('UPLOADS_DIR', APP_ROOT . '/uploads');
define('LOGS_DIR', APP_ROOT . '/logs');
define('CACHE_DIR', APP_ROOT . '/cache');
define('TEMP_DIR', APP_ROOT . '/temp');

// File permissions
define('DIR_PERMISSIONS', 0755);
define('FILE_PERMISSIONS', 0644);

// Upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

// Pagination settings
define('ITEMS_PER_PAGE', 10);
define('PAGE_RANGE', 2);

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 hour

// Logging settings
define('LOG_ENABLED', true);
define('LOG_LEVEL', 'debug'); // debug, info, warning, error, critical
define('LOG_FORMAT', '[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n');

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_MAX_LENGTH', 72);
define('PASSWORD_REQUIRE_SPECIAL', true);
define('PASSWORD_REQUIRE_NUMBERS', true);
define('PASSWORD_REQUIRE_UPPERCASE', true);
define('PASSWORD_REQUIRE_LOWERCASE', true);

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', true);
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');

// API settings
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per minute
define('API_TOKEN_LIFETIME', 3600); // 1 hour

// Email settings
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', '');
define('MAIL_FROM_NAME', APP_NAME);

// Shipment settings
define('SHIPMENT_STATUSES', [
    'pending' => 'Pending',
    'processing' => 'Processing',
    'in_transit' => 'In Transit',
    'delivered' => 'Delivered',
    'cancelled' => 'Cancelled'
]);

define('SHIPMENT_PRIORITIES', [
    'low' => 'Low',
    'medium' => 'Medium',
    'high' => 'High',
    'urgent' => 'Urgent'
]);

// User roles
define('USER_ROLES', [
    'admin' => 'Administrator',
    'manager' => 'Manager',
    'staff' => 'Staff',
    'user' => 'User'
]);

// Error messages
define('ERROR_MESSAGES', [
    'invalid_credentials' => 'Invalid username or password',
    'account_locked' => 'Account is locked. Please try again later',
    'session_expired' => 'Your session has expired. Please login again',
    'permission_denied' => 'You do not have permission to access this resource',
    'invalid_request' => 'Invalid request',
    'not_found' => 'Resource not found',
    'server_error' => 'An error occurred. Please try again later'
]);

// Success messages
define('SUCCESS_MESSAGES', [
    'login_success' => 'Login successful',
    'logout_success' => 'Logout successful',
    'registration_success' => 'Registration successful',
    'password_reset_success' => 'Password reset successful',
    'profile_update_success' => 'Profile updated successfully',
    'shipment_create_success' => 'Shipment created successfully',
    'shipment_update_success' => 'Shipment updated successfully',
    'shipment_delete_success' => 'Shipment deleted successfully'
]);

// Validation messages
define('VALIDATION_MESSAGES', [
    'required' => 'The :field field is required',
    'email' => 'The :field must be a valid email address',
    'min_length' => 'The :field must be at least :min characters',
    'max_length' => 'The :field must not exceed :max characters',
    'numeric' => 'The :field must be a number',
    'date' => 'The :field must be a valid date',
    'file_type' => 'The :field must be a valid file type',
    'file_size' => 'The :field must not exceed :max size'
]); 