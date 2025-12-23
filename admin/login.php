<?php
// admin/login.php - Admin Login Only
require_once '../config/database.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emailOrUsername = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);

    if (empty($emailOrUsername) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $conn = getDatabaseConnection();
            $sql = "SELECT * FROM users WHERE (username = '$emailOrUsername' OR email = '$emailOrUsername') AND status = 'active'";
            $result = $conn->query($sql);
            $admin = $result->fetch_assoc();

            if ($admin && $admin['role'] === 'admin' && $password === $admin['password']) {
                $_SESSION['user_id'] = $admin['user_id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['full_name'] = $admin['full_name'];
                $_SESSION['role'] = $admin['role'];

                redirect('dashboard.php');
            } else {
                $error = 'Invalid admin credentials.';
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
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Login - StylePro Essentials</title>
<link href="assests/css/home.css" rel="stylesheet"></head>
<body>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - StylePro Essentials</title>
    <link href="../assests/css/auth.css" rel="stylesheet">
    <link href="../assests/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2>Admin Login</h2>
            <p>Sign in to manage the admin dashboard</p>
        </div>
        <form class="auth-form" method="POST">
            <div class="form-group">
                <label for="email">Email or Username</label>
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
            <a href="../index.php">← Return to homepage</a><br>
            <a href="../customer/login.php" style="color: #007BFF; font-size: 14px;">→ Customer Login</a>
        </div>
    </div>
</body>
</html>
</body></html>