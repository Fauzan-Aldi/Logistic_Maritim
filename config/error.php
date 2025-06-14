<?php
// Error handling configuration

require_once 'environment.php';
require_once 'logging.php';

// Custom error handler
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $error_type = match($errno) {
        E_ERROR => 'E_ERROR',
        E_WARNING => 'E_WARNING',
        E_PARSE => 'E_PARSE',
        E_NOTICE => 'E_NOTICE',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_STRICT => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED => 'E_DEPRECATED',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        default => 'UNKNOWN'
    };
    
    $message = sprintf(
        "%s: %s in %s on line %d",
        $error_type,
        $errstr,
        $errfile,
        $errline
    );
    
    log_error($message);
    
    if (is_development()) {
        echo "<pre>$message</pre>";
    } else {
        echo ERROR_MESSAGES['server_error'];
    }
    
    return true;
}

// Custom exception handler
function custom_exception_handler($exception) {
    $message = sprintf(
        "Uncaught Exception: %s in %s on line %d\nStack trace:\n%s",
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );
    
    log_error($message);
    
    if (is_development()) {
        echo "<pre>$message</pre>";
    } else {
        echo ERROR_MESSAGES['server_error'];
    }
}

// Shutdown function
function shutdown_function() {
    $error = error_get_last();
    
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $message = sprintf(
            "Fatal Error: %s in %s on line %d",
            $error['message'],
            $error['file'],
            $error['line']
        );
        
        log_error($message);
        
        if (is_development()) {
            echo "<pre>$message</pre>";
        } else {
            echo ERROR_MESSAGES['server_error'];
        }
    }
}

// Set error handlers
set_error_handler('custom_error_handler');
set_exception_handler('custom_exception_handler');
register_shutdown_function('shutdown_function');

// Database error handler
function handle_database_error($error, $query = '') {
    $message = sprintf(
        "Database Error: %s\nQuery: %s",
        $error,
        $query
    );
    
    log_error($message);
    
    if (is_development()) {
        echo "<pre>$message</pre>";
    } else {
        echo ERROR_MESSAGES['server_error'];
    }
}

// File operation error handler
function handle_file_error($operation, $file_path, $error) {
    $message = sprintf(
        "File Operation Error: %s - %s - %s",
        $operation,
        $file_path,
        $error
    );
    
    log_error($message);
    
    if (is_development()) {
        echo "<pre>$message</pre>";
    } else {
        echo ERROR_MESSAGES['server_error'];
    }
}

// API error handler
function handle_api_error($status_code, $message, $details = []) {
    $error_message = sprintf(
        "API Error: %d - %s\nDetails: %s",
        $status_code,
        $message,
        json_encode($details)
    );
    
    log_error($error_message);
    
    if (is_development()) {
        echo "<pre>$error_message</pre>";
    } else {
        echo ERROR_MESSAGES['server_error'];
    }
}

// Validation error handler
function handle_validation_error($field, $message) {
    $error_message = sprintf(
        "Validation Error: %s - %s",
        $field,
        $message
    );
    
    log_warning($error_message);
    
    return $error_message;
}

// Security error handler
function handle_security_error($type, $message, $details = []) {
    $error_message = sprintf(
        "Security Error: %s - %s\nDetails: %s",
        $type,
        $message,
        json_encode($details)
    );
    
    log_error($error_message);
    
    if (is_development()) {
        echo "<pre>$error_message</pre>";
    } else {
        echo ERROR_MESSAGES['server_error'];
    }
}

// Error helper functions
function is_error($value) {
    return $value instanceof Error || $value instanceof Exception;
}

function get_error_message($error) {
    if ($error instanceof Error || $error instanceof Exception) {
        return $error->getMessage();
    }
    return (string) $error;
}

function get_error_trace($error) {
    if ($error instanceof Error || $error instanceof Exception) {
        return $error->getTraceAsString();
    }
    return '';
}

function format_error($error) {
    if ($error instanceof Error || $error instanceof Exception) {
        return sprintf(
            "%s in %s on line %d\nStack trace:\n%s",
            $error->getMessage(),
            $error->getFile(),
            $error->getLine(),
            $error->getTraceAsString()
        );
    }
    return (string) $error;
} 