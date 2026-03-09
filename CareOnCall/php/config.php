<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'careoncall');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Session configuration
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Base URL
define('BASE_URL', 'http://localhost/CareOnCall/');

// Encryption key for sensitive data
define('ENCRYPTION_KEY', 'your-secret-key-here-change-in-production');

// Function to escape user input
function escape_string($string) {
    global $conn;
    return $conn->real_escape_string($string);
}

// Function to hash passwords
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Function to verify passwords
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Function to redirect
function redirect($url) {
    header('Location: ' . BASE_URL . $url);
    exit();
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to get current user
function get_current_user() {
    global $conn;
    if (!is_logged_in()) {
        return null;
    }
    
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
    return $result->fetch_assoc();
}

// Function to check user role
function has_role($required_role) {
    $user = get_current_user();
    return $user && $user['user_type'] === $required_role;
}
?>
