<?php
// admin/usermanagement.php - Manage Users
require_once '../config/database.php';
requireAdminLogin();

$user = getCurrentUser();
$conn = getDatabaseConnection();

// ---- Flash helper ----
function flash($key) {
    if (!empty($_SESSION[$key])) {
        $m = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $m;
    }
    return '';
}

// ---- DELETE USER (GET) ----
if (isset($_GET['delete_id'])) {
    $user_id = (int) $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $_SESSION['msg'] = $stmt->execute() ? "User deleted successfully." : "User deletion failed.";
    header("Location: usermanagement.php");
    exit();
}

// ---- ADD USER (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $role      = trim($_POST['role'] ?? 'staff');
    $password  = $_POST['password'] ?? '';
    $status    = 'active';

    // Basic validation
    if ($full_name !== '' && $username !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $password !== '') {
        // Optional: check duplicate username/email
        $du = $conn->prepare("SELECT COUNT(*) c FROM users WHERE username=? OR email=?");
        $du->bind_param("ss", $username, $email);
        $du->execute();
        $du_res = $du->get_result()->fetch_assoc();
        if (!empty($du_res['c']) && (int)$du_res['c'] > 0) {
            $_SESSION['msg'] = "Username or email already exists.";
        } else {
            // Hash password
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare(
                "INSERT INTO users (full_name, username, email, password, role, status, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->bind_param("ssssss", $full_name, $username, $email, $hash, $role, $status);
            $_SESSION['msg'] = $stmt->execute() ? "User added successfully." : "User addition failed.";
        }
    } else {
        $_SESSION['msg'] = "Please fill out all fields correctly.";
    }

    header("Location: usermanagement.php");
    exit();
}

// ---- FETCH USERS ----
$users_result = $conn->query("SELECT * FROM users ORDER BY full_name ASC");

// Badge helper
function roleBadgeClass($role) {
    return (strtolower($role) === 'admin') ? 'bg-primary' : 'bg-secondary';
}
function statusBadgeClass($status) {
    return ($status === 'active') ? 'bg-success' : 'bg-secondary';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Hair Care Store</title>
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/admindashboard.css" rel="stylesheet">
    <style>
        /* Small polish to align with your admin theme */
        .form-grid { display:grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .form-grid .wide { grid-column: span 2; }
        @media (max-width: 1100px) { .form-grid { grid-template-columns: 1fr 1fr; } .form-grid .wide { grid-column: span 2; } }
        @media (max-width: 700px) { .form-grid { grid-template-columns: 1fr; } .form-grid .wide { grid-column: span 1; } }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
        .form-group input, .form-group select {
            width:100%; padding:10px 12px; border:1px solid #e4e8ee; border-radius:10px; background:#fbfcfe;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="text-center mb-4">
            <div style="width:60px;height:60px;background:#007bff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;"></div>
            <h6 class="text-white mt-2 mb-1">Admin Panel</h6>
            <small class="text-muted">Welcome, <?php echo htmlspecialchars($user['name'] ?? $user['username'] ?? 'Admin'); ?></small>
        </div>
        <ul class="nav">
            <li><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <li><a class="nav-link" href="productmanagement.php">Product Management</a></li>
            <li><a class="nav-link" href="ordermanagement.php">Order Management</a></li>
            <li><a class="nav-link" href="customermanagement.php">Customer Management</a></li>
            <li><a class="nav-link active" href="usermanagement.php">User Management</a></li>
            <li><a class="nav-link" href="report&analytics.php">Reports &amp; Analytics</a></li>
            <li><a class="nav-link" href="../index.php" target="_blank">View Website</a></li>
            <li><a class="nav-link text-danger" href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="admin-main-content">
        <?php if ($m = flash('msg')): ?>
            <div class="alert alert-info" style="border-radius:12px;"><?php echo htmlspecialchars($m); ?></div>
        <?php endif; ?>

        <div class="dashboard-header">
            <div>
                <h1>User Management</h1>
                <p>Manage platform users</p>
            </div>
        </div>

        <!-- Add User Form -->
        <div class="card">
            <div class="card-header">
                <h6>Add New User</h6>
            </div>
            <div class="card-body">
                <form action="usermanagement.php" method="POST" autocomplete="off">
                    <div class="form-grid">
                        <div class="form-group wide">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group wide">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group wide">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                    </div>
                    <div style="margin-top:14px; display:flex; gap:10px;">
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                        <button type="reset" class="btn btn-outline-success">Reset</button>
                    </div>
                    <p class="text-muted" style="margin-top:8px;">Passwords are stored securely using hashing.</p>
                </form>
            </div>
        </div>

        <!-- All Users Table -->
        <div class="card" style="margin-top:22px;">
            <div class="card-header">
                <h6>All Users</h6>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($users_result && $users_result->num_rows > 0): ?>
                        <?php while ($row = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo roleBadgeClass($row['role']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($row['role'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo statusBadgeClass($row['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <a href="edit_user.php?user_id=<?php echo (int)$row['user_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?delete_id=<?php echo (int)$row['user_id']; ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                <h5 class="text-muted">No users found</h5>
                                <p class="text-muted">Add your first user using the form above.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /.admin-main-content -->
</div><!-- /.container-fluid -->
</body>
</html>
