<?php
// admin/index.php - Admin Login Page
require_once '../config/database.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

// Use global mysqli connection from config
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // UNSAFE raw SQL query (not using prepare)
        $sql = "SELECT * FROM users WHERE (username = '$username' OR email = '$username') AND status = 'active'";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            redirect('dashboard.php');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Login - Hair Care Store</title>
<link href="assests/css/home.css" rel="stylesheet"></head>
<body>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hair Care Store</title>
    <link href="../assests/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card login-card">
                <div class="card-header login-header text-center py-4">
                    <h3>Admin Login</h3>
                    <p class="mb-0">Hair Care Store Admin Panel</p>
                </div>
                <div class="card-body p-4">

                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                Username or Email
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="" 
                                   placeholder="Enter your username or email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter your password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            Login to Dashboard
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <a href="../index.php" class="text-muted text-decoration-none">
                            Back to Website
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Test Credentials Card -->
            <div class="card mt-3 credentials-card">
                <div class="card-header bg-info text-white text-center">
                    <h6 class="mb-0">Team Administrator Credentials</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <strong class="text-primary">Rojina Dhingri</strong><br>
                                <small class="text-muted">Username: <code>rojina</code></small><br>
                                <small class="text-muted">Password: <code>web@password</code></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <strong class="text-primary">Prinsha Joshi</strong><br>
                                <small class="text-muted">Username: <code>prinsha</code></small><br>
                                <small class="text-muted">Password: <code>web@password</code></small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded text-center">
                                <strong class="text-primary">Shrutina</strong><br>
                                <small class="text-muted"><code>shrutina</code></small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded text-center">
                                <strong class="text-primary">Pamas</strong><br>
                                <small class="text-muted"><code>pamas</code></small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded text-center">
                                <strong class="text-primary">Kushal</strong><br>
                                <small class="text-muted"><code>kushal</code></small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            All passwords: <strong>web@password</strong>
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Quick Test Buttons -->
            <div class="card mt-2 credentials-card">
                <div class="card-body p-2">
                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                        <button class="btn btn-outline-primary btn-sm" onclick="quickFill('rojina')">
                            Rojina
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickFill('prinsha')">
                            Prinsha
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickFill('shrutina')">
                            Shrutina
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickFill('pamas')">
                            Pamas
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickFill('kushal')">
                            Kushal
                        </button>
                    </div>
                    <div class="text-center mt-1">
                        <small class="text-muted">Click to auto-fill credentials</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Quick fill function for testing
        function quickFill(username) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = 'web@password';
            
            // Add visual feedback
            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('password');
            
            usernameField.classList.add('bg-success', 'text-white');
            passwordField.classList.add('bg-success', 'text-white');
            
            setTimeout(() => {
                usernameField.classList.remove('bg-success', 'text-white');
                passwordField.classList.remove('bg-success', 'text-white');
            }, 1000);
        }
        
        // Add enter key support
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const form = document.querySelector('form');
                if (form) {
                    form.submit();
                }
            }
        });
        
        // Focus on username field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>

</body></html>