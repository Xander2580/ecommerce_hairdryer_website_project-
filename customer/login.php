<?php
// customer/login.php - Customer Login Only 
require_once '../config/database.php';

// Redirect if already logged in
if (isCustomerLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if (isset($_GET['registered'])) {
    $success = 'Registration successful! Please login with your credentials.';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $conn = getDatabaseConnection();

            // Only customer login
            $sql = "SELECT * FROM customers WHERE email = '$email' AND status = 'active'";
            $result = mysqli_query($conn, $sql);
            $customer = mysqli_fetch_assoc($result);

            if ($customer && $password === $customer['password']) {
                $_SESSION['customer_id'] = $customer['customer_id'];
                $_SESSION['customer_name'] = $customer['first_name'] . ' ' . $customer['last_name'];
                $_SESSION['customer_email'] = $customer['email'];

                redirect('dashboard.php');
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Login - StylePro Essentials</title>
<link href="assests/css/home.css" rel="stylesheet"></head>
<body>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StylePro Essentials</title>
    <link href="../assests/css/auth.css" rel="stylesheet">
    <link href="../assests/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2>Welcome Back</h2>
            <p>Sign in to your customer account to continue</p>
        </div>
        <form class="auth-form" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="text" class="form-control" id="email" name="email"
                       value="" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-auth">Sign In</button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Create one</a></p>
            <a href="../index.php">← Return to homepage</a><br>
            <a href="../admin/login.php" style="color: #007BFF; font-size: 14px;">→ Admin Login</a>
        </div>
    </div>
</body>
</html>
