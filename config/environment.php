<?php
// Environment configuration

// Define environment
define('ENVIRONMENT', 'development'); // development, production

// Environment-specific settings
$config = [
    'development' => [
        'display_errors' => true,
        'error_reporting' => E_ALL,
        'database' => [
            'host' => 'localhost',
            'name' => 'logistik_maritim',
            'user' => 'root',
            'pass' => '',
            'charset' => 'utf8mb4'
        ],
        'session' => [
            'lifetime' => 3600,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ],
        'cache' => [
            'enabled' => false,
            'path' => dirname(__DIR__) . '/cache',
            'lifetime' => 3600
        ],
        'logging' => [
            'enabled' => true,
            'path' => dirname(__DIR__) . '/logs',
            'level' => 'debug'
        ],
        'debug' => true,
        'maintenance' => false
    ],
    'production' => [
        'display_errors' => false,
        'error_reporting' => E_ALL & ~E_DEPRECATED & ~E_STRICT,
        'database' => [
            'host' => 'localhost',
            'name' => 'others_rita-poltek_logistik',
            'user' => 'production_user',
            'pass' => 'production_password',
            'charset' => 'utf8mb4'
        ],
        'session' => [
            'lifetime' => 3600,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ],
        'cache' => [
            'enabled' => true,
            'path' => dirname(__DIR__) . '/cache',
            'lifetime' => 3600
        ],
        'logging' => [
            'enabled' => true,
            'path' => dirname(__DIR__) . '/logs',
            'level' => 'error'
        ],
        'debug' => false,
        'maintenance' => false
    ]
];

// Apply environment settings
$env_config = $config[ENVIRONMENT];

// Error reporting
ini_set('display_errors', $env_config['display_errors']);
error_reporting($env_config['error_reporting']);

// Database constants
define('DB_HOST', $env_config['database']['host']);
define('DB_NAME', $env_config['database']['name']);
define('DB_USER', $env_config['database']['user']);
define('DB_PASS', $env_config['database']['pass']);
define('DB_CHARSET', $env_config['database']['charset']);

// Session constants
define('SESSION_LIFETIME', $env_config['session']['lifetime']);
define('SESSION_PATH', $env_config['session']['path']);
define('SESSION_DOMAIN', $env_config['session']['domain']);
define('SESSION_SECURE', $env_config['session']['secure']);
define('SESSION_HTTPONLY', $env_config['session']['httponly']);
define('SESSION_SAMESITE', $env_config['session']['samesite']);

// Cache constants
define('CACHE_ENABLED', $env_config['cache']['enabled']);
define('CACHE_PATH', $env_config['cache']['path']);
define('CACHE_LIFETIME', $env_config['cache']['lifetime']);

// Logging constants
define('LOGGING_ENABLED', $env_config['logging']['enabled']);
define('LOGGING_PATH', $env_config['logging']['path']);
define('LOGGING_LEVEL', $env_config['logging']['level']);

// Debug and maintenance mode
define('DEBUG_MODE', $env_config['debug']);
define('MAINTENANCE_MODE', $env_config['maintenance']);

// Environment helper functions
function is_development()
{
    return ENVIRONMENT === 'development';
}

function is_production()
{
    return ENVIRONMENT === 'production';
}

function get_setting($key, $default = null)
{
    global $env_config;
    return $env_config[$key] ?? $default;
}

function is_maintenance_mode()
{
    return MAINTENANCE_MODE;
}

function is_debug_mode()
{
    return DEBUG_MODE;
}

// Function to get environment
function get_environment()
{
    return ENVIRONMENT;
}

// Function to get database setting
function get_database_setting($key, $default = null)
{
    global $env_config;
    return $env_config['database'][$key] ?? $default;
}

// Function to get session setting
function get_session_setting($key, $default = null)
{
    global $env_config;
    return $env_config['session'][$key] ?? $default;
}

// Function to get cache setting
function get_cache_setting($key, $default = null)
{
    global $env_config;
    return $env_config['cache'][$key] ?? $default;
}

// Function to get logging setting
function get_logging_setting($key, $default = null)
{
    global $env_config;
    return $env_config['logging'][$key] ?? $default;
}
