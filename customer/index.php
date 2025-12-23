<?php
// customer/index.php - Customer Index Page
require_once '../config/database.php';

// Check if customer is logged in
if (isCustomerLoggedIn()) {
    // If logged in, redirect to dashboard
    redirect('dashboard.php');
} else {
    // If not logged in, redirect to login page
    redirect('login.php');
}
?> 