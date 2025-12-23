<?php
// customer/register.php - Customer Registration 
require_once '../config/database.php';

// Redirect if already logged in
if (isCustomerLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $city = sanitize_input($_POST['city']);
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 2) {
        $error = 'Password must be at least 2 characters long.';
    } else {
        try {
            $conn = getDatabaseConnection();
            
            // Check if email already exists 
            $sql = "SELECT customer_id FROM customers WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) > 0) {
                $error = 'Email address already registered.';
            } else {
                // Insert new customer 
                $sql = "INSERT INTO customers (first_name, last_name, email, password, phone, address, city, state, country) 
                        VALUES ('$first_name', '$last_name', '$email', '$password', '$phone', '$address', '$city', 'Bagmati', 'Nepal')";
                
                $res = mysqli_query($conn, $sql);
                if ($res) {
                    redirect('login.php?registered=1');
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        } catch(Exception $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Register</title>
<link href="assests/css/home.css" rel="stylesheet"></head>
<body>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="../assests/css/auth.css" rel="stylesheet">
    <link href="../assests/css/register.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2>Create Account</h2>
            <p>Get started with your free account</p>
        </div>
        <form class="auth-form" method="POST">
            <div class="row-fields">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" 
                           value="" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" 
                           value="" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="" required>
            </div>
            
            <div class="row-fields">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" 
                       value="">
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" name="city" 
                       value="">
            </div>
            
            <button type="submit" class="btn-auth">Create Account</button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Sign in</a></p>
            <a href="../index.php">← Return to homepage</a>
        </div>
    </div>
</body>
</html>
