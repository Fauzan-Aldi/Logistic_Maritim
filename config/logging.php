<?php
// Logging configuration

require_once 'environment.php';

class Logger {
    private static $instance = null;
    private $log_path;
    private $enabled;
    private $level;
    private $levels = [
        'debug' => 0,
        'info' => 1,
        'warning' => 2,
        'error' => 3,
        'critical' => 4
    ];
    
    private function __construct() {
        $this->log_path = LOGGING_PATH;
        $this->enabled = LOGGING_ENABLED;
        $this->level = $this->levels[LOGGING_LEVEL] ?? 0;
        
        if ($this->enabled && !is_dir($this->log_path)) {
            mkdir($this->log_path, 0755, true);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function log($level, $message, $context = []) {
        if (!$this->enabled || $this->levels[$level] < $this->level) {
            return false;
        }
        
        $log_file = $this->log_path . '/' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $context_str = !empty($context) ? json_encode($context) : '';
        
        $log_message = sprintf(
            "[%s] [%s] %s %s\n",
            $timestamp,
            strtoupper($level),
            $message,
            $context_str
        );
        
        return file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
    }
    
    public function debug($message, $context = []) {
        return $this->log('debug', $message, $context);
    }
    
    public function info($message, $context = []) {
        return $this->log('info', $message, $context);
    }
    
    public function warning($message, $context = []) {
        return $this->log('warning', $message, $context);
    }
    
    public function error($message, $context = []) {
        return $this->log('error', $message, $context);
    }
    
    public function critical($message, $context = []) {
        return $this->log('critical', $message, $context);
    }
    
    public function logException($exception, $context = []) {
        $message = sprintf(
            "Exception: %s in %s on line %d\nStack trace:\n%s",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        
        return $this->error($message, $context);
    }
    
    public function logDatabaseError($error, $query = '', $context = []) {
        $message = sprintf(
            "Database Error: %s\nQuery: %s",
            $error,
            $query
        );
        
        return $this->error($message, $context);
    }
    
    public function logSecurityEvent($event_type, $description, $context = []) {
        $message = sprintf(
            "Security Event: %s - %s",
            $event_type,
            $description
        );
        
        return $this->warning($message, $context);
    }
    
    public function logUserActivity($user_id, $action, $description, $context = []) {
        $message = sprintf(
            "User Activity: User ID %d - %s - %s",
            $user_id,
            $action,
            $description
        );
        
        return $this->info($message, $context);
    }
    
    public function logApiRequest($method, $endpoint, $status_code, $response_time, $context = []) {
        $message = sprintf(
            "API Request: %s %s - Status: %d - Time: %dms",
            $method,
            $endpoint,
            $status_code,
            $response_time
        );
        
        return $this->info($message, $context);
    }
    
    public function logFileOperation($operation, $file_path, $status, $context = []) {
        $message = sprintf(
            "File Operation: %s - %s - Status: %s",
            $operation,
            $file_path,
            $status
        );
        
        return $this->info($message, $context);
    }
    
    public function rotateLogs($max_age = 30) {
        if (!$this->enabled) {
            return false;
        }
        
        $files = glob($this->log_path . '/*.log');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $max_age * 24 * 60 * 60) {
                    unlink($file);
                }
            }
        }
        
        return true;
    }
}

// Logging helper functions
function log_debug($message, $context = []) {
    return Logger::getInstance()->debug($message, $context);
}

function log_info($message, $context = []) {
    return Logger::getInstance()->info($message, $context);
}

function log_warning($message, $context = []) {
    return Logger::getInstance()->warning($message, $context);
}

function log_error($message, $context = []) {
    return Logger::getInstance()->error($message, $context);
}

function log_critical($message, $context = []) {
    return Logger::getInstance()->critical($message, $context);
}

function log_exception($exception, $context = []) {
    return Logger::getInstance()->logException($exception, $context);
}

function log_database_error($error, $query = '', $context = []) {
    return Logger::getInstance()->logDatabaseError($error, $query, $context);
}

function log_security_event($event_type, $description, $context = []) {
    return Logger::getInstance()->logSecurityEvent($event_type, $description, $context);
}

function log_user_activity($user_id, $action, $description, $context = []) {
    return Logger::getInstance()->logUserActivity($user_id, $action, $description, $context);
}

function log_api_request($method, $endpoint, $status_code, $response_time, $context = []) {
    return Logger::getInstance()->logApiRequest($method, $endpoint, $status_code, $response_time, $context);
}

function log_file_operation($operation, $file_path, $status, $context = []) {
    return Logger::getInstance()->logFileOperation($operation, $file_path, $status, $context);
}

function rotate_logs($max_age = 30) {
    return Logger::getInstance()->rotateLogs($max_age);
} 