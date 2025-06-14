<?php
// Security helper functions

require_once 'security.php';

function hash_password($password) {
    return Security::getInstance()->hashPassword($password);
}

function verify_password($password, $hash) {
    return Security::getInstance()->verifyPassword($password, $hash);
}

function generate_token($length = null) {
    return Security::getInstance()->generateToken($length);
}

function generate_csrf_token() {
    return Security::getInstance()->generateCSRFToken();
}

function validate_csrf_token($token) {
    return Security::getInstance()->validateCSRFToken($token);
}

function sanitize_input($input) {
    return Security::getInstance()->sanitizeInput($input);
}

function validate_email($email) {
    return Security::getInstance()->validateEmail($email);
}

function validate_file($file, $type = null, $size = null) {
    return Security::getInstance()->validateFile($file, $type, $size);
}

function check_login_attempts($username) {
    return Security::getInstance()->checkLoginAttempts($username);
}

function record_login_attempt($username, $success) {
    return Security::getInstance()->recordLoginAttempt($username, $success);
}

function regenerate_session() {
    return Security::getInstance()->regenerateSession();
}

function validate_origin($origin) {
    return Security::getInstance()->validateOrigin($origin);
}

function set_security_headers() {
    return Security::getInstance()->setSecurityHeaders();
} 