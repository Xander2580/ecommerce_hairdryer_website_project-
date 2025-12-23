<?php
session_start();

// Database configuration
define('DB_HOST', '127.0.0.1'); 
define('DB_USER', 'root'); 
define('DB_PASS', '');
define('DB_NAME', 'hairdryer_straightner'); 

function getDatabaseConnection() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Opps! Database connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
    }
    return $conn;
}

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Authentication functions
function isCustomerLoggedIn() {
    return isset($_SESSION['customer_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function requireCustomerLogin() {
    if (!isCustomerLoggedIn()) {
        redirect('../customer/login.php');
    }
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        redirect('index.php');
    }
}

// Get current customer info - Professor's style
function getCurrentCustomer() {
    if (!isCustomerLoggedIn()) return null;
    
    $conn = getDatabaseConnection();
    $customer_id = $_SESSION['customer_id'];
    $sql = "SELECT * FROM customers WHERE customer_id = $customer_id";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// Get current admin user info - Professor's style
function getCurrentUser() {
    if (!isAdminLoggedIn()) return null;
    
    $conn = getDatabaseConnection();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE user_id = $user_id";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// Format price
function formatPrice($price) {
    return 'Rs. ' . number_format($price, 2);
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Generate order number
function generateOrderNumber() {
    return 'ORD-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}
?>
