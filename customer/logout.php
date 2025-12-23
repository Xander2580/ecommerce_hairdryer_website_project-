<?php
// customer/logout.php
require_once '../config/database.php';

// Destroy customer session
unset($_SESSION['customer_id']);
unset($_SESSION['customer_name']);
unset($_SESSION['customer_email']);

// Destroy entire session
session_destroy();

// Redirect to homepage
redirect('../index.php?logout=1');
?>
