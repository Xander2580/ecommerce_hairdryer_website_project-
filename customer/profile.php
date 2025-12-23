<?php
// customer/profile.php - Complete Customer Profile Management
require_once '../config/database.php';
requireCustomerLogin();

$customer = getCurrentCustomer();
$conn = getDatabaseConnection();
$success = '';
$error = '';

// Handle profile update 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $first_name = sanitize_input($_POST['first_name']);
        $last_name = sanitize_input($_POST['last_name']);
        $phone = sanitize_input($_POST['phone']);
        $address = sanitize_input($_POST['address']);
        $city = sanitize_input($_POST['city']);
        $state = sanitize_input($_POST['state']);
        $postal_code = sanitize_input($_POST['postal_code']);
        $date_of_birth = sanitize_input($_POST['date_of_birth']);
        $gender = sanitize_input($_POST['gender']);
        
        if (empty($first_name) || empty($last_name)) {
            $error = 'First name and last name are required.';
        } else {
            try {
                $customer_id = $_SESSION['customer_id'];
                
                // Handle null values properly
                $date_of_birth_value = $date_of_birth ?: 'NULL';
                $gender_value = $gender ?: 'NULL';
                
                // Update using professor's style
                $sql = "UPDATE customers SET 
                        first_name = '$first_name', last_name = '$last_name', phone = '$phone', address = '$address', 
                        city = '$city', state = '$state', postal_code = '$postal_code', 
                        date_of_birth = " . ($date_of_birth_value == 'NULL' ? 'NULL' : "'$date_of_birth_value'") . ", 
                        gender = " . ($gender_value == 'NULL' ? 'NULL' : "'$gender_value'") . ",
                        updated_at = CURRENT_TIMESTAMP
                        WHERE customer_id = $customer_id";
                
                $res = mysqli_query($conn, $sql);
                if ($res) {
                    $success = 'Profile updated successfully!';
                    
                    // Update session name if changed
                    $_SESSION['customer_name'] = $first_name . ' ' . $last_name;
                    
                    // Refresh customer data
                    $customer = getCurrentCustomer();
                } else {
                    $error = 'Error updating profile.';
                }
                
            } catch(Exception $e) {
                $error = 'Error updating profile: ' . $e->getMessage();
            }
        }
    }
    
    // Handle password change 
    if (isset($_POST['change_password'])) {
        $current_password = sanitize_input($_POST['current_password']);
        $new_password = sanitize_input($_POST['new_password']);
        $confirm_password = sanitize_input($_POST['confirm_password']);
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'All password fields are required.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } elseif (strlen($new_password) < 2) {
            $error = 'New password must be at least 2 characters long.';
        } elseif ($current_password !== $customer['password']) {
            $error = 'Current password is incorrect.';
        } else {
            try {
                $customer_id = $_SESSION['customer_id'];
                $sql = "UPDATE customers SET password = '$new_password', updated_at = CURRENT_TIMESTAMP WHERE customer_id = $customer_id";
                $res = mysqli_query($conn, $sql);
                if ($res) {
                    $success = 'Password changed successfully!';
                    
                    // Refresh customer data
                    $customer = getCurrentCustomer();
                } else {
                    $error = 'Error changing password.';
                }
                
            } catch(Exception $e) {
                $error = 'Error changing password: ' . $e->getMessage();
            }
        }
    }
}

// Get customer order statistics for profile summary 
try {
    $customer_id = $_SESSION['customer_id'];
    
    // Total orders
    $sql = "SELECT COUNT(*) as total FROM orders WHERE customer_id = $customer_id";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $total_orders = $data['total'];
    
    // Total spent
    $sql = "SELECT SUM(final_amount) as total FROM orders WHERE customer_id = $customer_id AND payment_status = 'paid'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $total_spent = $data['total'] ?? 0;
    
    // Last order date
    $sql = "SELECT MAX(created_at) as last_order FROM orders WHERE customer_id = $customer_id";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $last_order_date = $data['last_order'];
    
} catch(Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - StylePro Essentials</title>
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/customerdashboard.css" rel="stylesheet">
    <link href="../assests/css/profile.css" rel="stylesheet">
</head>
<body data-page="profile">
    <!-- Navigation Include -->
    <div data-include="../includes/navbar.php"></div>

    <div class="container-fluid my-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="sidebar-nav">
                    <!-- Profile Summary - Match dashboard style -->
                    <div class="text-center mb-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: white; font-size: 1.5rem;">
                            <?= strtoupper(substr($customer['first_name'],0,1).substr($customer['last_name'],0,1)) ?>
                        </div>
                        <h5 class="mt-2 mb-0"><?= htmlspecialchars($customer['first_name'].' '.$customer['last_name']) ?></h5>
                        <small class="text-muted"><?= htmlspecialchars($customer['email']) ?></small>
                        <div class="mt-1">
                            <span class="badge bg-success">
                                Active
                            </span>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="profile.php">
                                My Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
                                My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
                                Browse Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                Shopping Cart
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">
                                Back to Store
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Alert Messages (Static example) -->
                <div class="alert alert-success alert-dismissible fade show" style="display: none;">
                    Profile updated successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                
                <div class="alert alert-danger alert-dismissible fade show" style="display: none;">
                    Error updating profile.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <!-- Profile Information Form -->
                <div class="card profile-card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: white;">
                        <h3 class="mb-0">Personal Information</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-section">
                                <h3 class="section-title">Basic Information</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">
                                                First Name *
                                            </label>
                                            <!-- First Name -->
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($customer['first_name']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label">
                                                Last Name *
                                            </label>
                                            <!-- Last Name -->
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($customer['last_name']) ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">
                                                Email Address
                                            </label>
                                            <!-- Email (readonly) -->
                                            <input type="email" class="form-control bg-light" id="email" value="<?= htmlspecialchars($customer['email']) ?>" readonly>
                                            <!-- <small class="text-muted">Email cannot be changed. Contact support if needed.</small> -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">
                                                Phone Number
                                            </label>
                                            <!-- Phone -->
                                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" placeholder="+977-9xxxxxxxxx">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_of_birth" class="form-label">
                                                Date of Birth
                                            </label>
                                            <!-- Date of Birth -->
                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($customer['date_of_birth']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gender" class="form-label">
                                                Gender
                                            </label>
                                            <!-- Gender -->
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male" <?= ($customer['gender'] == 'male') ? 'selected' : '' ?>>Male</option>
                                                <option value="female" <?= ($customer['gender'] == 'female') ? 'selected' : '' ?>>Female</option>
                                                <option value="other" <?= ($customer['gender'] == 'other') ? 'selected' : '' ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h3 class="section-title">Address Information</h3>
                                <div class="mb-3">
                                    <label for="address" class="form-label">
                                        Street Address
                                    </label>
                                    <!-- Address -->
                                    <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your complete address"><?= htmlspecialchars($customer['address']) ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="city" class="form-label">
                                                City
                                            </label>
                                            <!-- City -->
                                            <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($customer['city']) ?>" placeholder="e.g. Kathmandu">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="state" class="form-label">
                                                State/Province
                                            </label>
                                            <!-- State -->
                                            <input type="text" class="form-control" id="state" name="state" value="<?= htmlspecialchars($customer['state']) ?>" placeholder="e.g. Bagmati">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="postal_code" class="form-label">
                                                Postal Code
                                            </label>
                                            <!-- Postal Code -->
                                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?= htmlspecialchars($customer['postal_code']) ?>" placeholder="e.g. 44600">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="country" class="form-label">
                                        Country
                                    </label>
                                    <input type="text" class="form-control bg-light" id="country" value="Nepal" readonly>
                                    <small class="text-muted">Currently serving Nepal only</small>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h3 class="section-title">Account Details</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Member Since
                                            </label>
                                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars(date('Y-m-d', strtotime($customer['created_at']))); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Account Status
                                            </label>
                                            <input type="text" class="form-control bg-light" value="Active" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">
                                        Last Order
                                    </label>
                                    <input type="text" class="form-control bg-light" value="<?= $last_order_date ? htmlspecialchars(date('Y-m-d', strtotime($last_order_date))) : 'No orders yet'; ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-secondary">
                                    Back to Dashboard
                                </a>
                                <button type="submit" name="update_profile" class="btn btn-gradient btn-lg">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Change Form -->
                <div class="card profile-card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: white;">
                        <h5 class="mb-0">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary">
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Account Activity Card -->
                <div class="card profile-card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: white;">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="activity-timeline">
                            <div class="timeline-item">
                                <strong>Account Created</strong>
                                <small class="text-muted d-block"><?= htmlspecialchars(date('Y-m-d', strtotime($customer['created_at']))) ?></small>
                                <p class="mb-0 text-muted">Welcome to StylePro Essentials!</p>
                            </div>
                            
                            <div class="timeline-item">
                                <strong>Profile Updated</strong>
                                <small class="text-muted d-block"><?= htmlspecialchars(date('Y-m-d', strtotime($customer['updated_at']))) ?></small>
                                <p class="mb-0 text-muted">Personal information was updated</p>
                            </div>
                            
                            <div class="timeline-item">
                                <strong>Last Order Placed</strong>
                                <small class="text-muted d-block"><?= $last_order_date ? htmlspecialchars(date('Y-m-d', strtotime($last_order_date))) : 'No orders yet'; ?></small>
                                <p class="mb-0 text-muted">Thank you for your purchase!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Include -->
    <div data-include="../includes/footer.php"></div>
    <script src="../assests/js/include.js"></script>
</body>
</html>