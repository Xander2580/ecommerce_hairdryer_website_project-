<?php
// admin/logout.php
require_once '../config/database.php';

// Destroy admin session
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['full_name']);
unset($_SESSION['role']);

// Destroy entire session
session_destroy();

// Redirect to admin login
redirect('../index.php');
?>
