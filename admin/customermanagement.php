<?php
// admin/customermanagement.php - Manage Customers
require_once '../config/database.php';
requireAdminLogin();

$user = getCurrentUser();
$conn = getDatabaseConnection();

// Flash helper
function flash($key) {
    if (!empty($_SESSION[$key])) {
        $m = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $m;
    }
    return '';
}

// --- DELETE CUSTOMER ---
if (isset($_GET['delete_id'])) {
    $customer_id = (int) $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM customers WHERE customer_id=?");
    $stmt->bind_param("i", $customer_id);
    $_SESSION['msg'] = $stmt->execute() ? "Customer deleted successfully." : "Customer deletion failed.";
    header("Location: customermanagement.php");
    exit();
}

// --- ADD / EDIT CUSTOMER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $status     = $_POST['status'] ?? 'inactive';

    if (isset($_POST['add_customer'])) {
        $stmt = $conn->prepare("INSERT INTO customers (first_name, last_name, email, phone, status) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $status);
        $_SESSION['msg'] = $stmt->execute() ? "Customer added successfully." : "Customer addition failed.";
        header("Location: customermanagement.php");
        exit();
    }

    if (isset($_POST['edit_customer'])) {
        $customer_id = (int) $_POST['customer_id'];
        $stmt = $conn->prepare("UPDATE customers SET first_name=?, last_name=?, email=?, phone=?, status=? WHERE customer_id=?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $status, $customer_id);
        $_SESSION['msg'] = $stmt->execute() ? "Customer updated successfully." : "Customer update failed.";
        header("Location: customermanagement.php");
        exit();
    }
}

/* ==========================
   FILTERS (GET)
   - status: all|active|inactive
   - q: search (name/email/phone)
   ========================== */
$valid_statuses = ['all','active','inactive'];
$status = strtolower(trim($_GET['status'] ?? 'all'));
$status = in_array($status, $valid_statuses, true) ? $status : 'all';
$q = trim($_GET['q'] ?? '');

$where = [];
$params = [];
$types  = "";

// Status filter
if ($status !== 'all') {
    $where[] = "status = ?";
    $params[] = $status;
    $types   .= "s";
}

// Search (name/email/phone)
if ($q !== '') {
    $like = "%$q%";
    $where[] = "(CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types   .= "sss";
}

$sql = "SELECT customer_id, first_name, last_name, email, phone, status FROM customers";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY first_name ASC, last_name ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$customers_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - Hair Care Store</title>
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/admindashboard.css" rel="stylesheet">
    <style>
        /* Page-scoped layout polish (no Bootstrap needed) */
        .form-grid-2 { display:grid; grid-template-columns: 1fr 1fr; gap:20px; }
        .form-grid-3 { display:grid; grid-template-columns: 1fr 1fr 1fr; gap:20px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
        .form-group input, .form-group select {
            width:100%; padding:10px 12px; border:1px solid #e4e8ee; border-radius:10px; background:#fbfcfe;
        }
        .filter-bar {
            display:grid; grid-template-columns: 1fr 2fr auto; gap:12px; align-items:end; margin-bottom:16px;
        }
        .filter-bar .form-group input, .filter-bar .form-group select { border-radius:10px; }
        @media (max-width: 1024px) {
            .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
            .filter-bar { grid-template-columns: 1fr; }
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
            <li><a class="nav-link active" href="customermanagement.php">Customer Management</a></li>
            <li><a class="nav-link" href="usermanagement.php">User Management</a></li>
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
                <h1>Customer Management</h1>
                <p>Manage your customer details</p>
            </div>
        </div>

        <!-- Add Customer Form -->
        <div class="card">
            <div class="card-header">
                <h6>Add New Customer</h6>
            </div>
            <div class="card-body">
                <form action="customermanagement.php" method="POST" novalidate>
                    <div class="form-grid-2" style="margin-bottom:20px;">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="form-grid-3" style="margin-bottom:12px;">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="add_customer" class="btn btn-primary">Add Customer</button>
                </form>
            </div>
        </div>

        <!-- Customer Table -->
        <div class="card" style="margin-top:22px;">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h6>All Customers</h6>

                <!-- Filter/Search Bar -->
                <form class="filter-bar" method="GET" action="customermanagement.php" style="margin:0;flex:1;max-width:680px;">
                    <div class="form-group">
                        <label for="status-filter">Status</label>
                        <select id="status-filter" name="status">
                            <?php
                            foreach ($valid_statuses as $s) {
                                $sel = ($status === $s) ? 'selected' : '';
                                echo "<option value=\"$s\" $sel>" . ucfirst($s) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="q">Search (Name / Email / Phone)</label>
                        <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="e.g., Sita Sharma or 98xxxxxxx">
                    </div>
                    <div class="form-group" style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="customermanagement.php" class="btn btn-outline-success">Reset</a>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <?php if ($customers_result && $customers_result->num_rows > 0): ?>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $customers_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td>
                                    <span class="badge <?php echo ($row['status'] === 'active') ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit.php?customer_id=<?php echo (int)$row['customer_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?delete_id=<?php echo (int)$row['customer_id']; ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-4">
                        <h5 class="text-muted">No customers found</h5>
                        <p class="text-muted">Try adjusting your filters or add a new customer above.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- /.admin-main-content -->
</div><!-- /.container-fluid -->
</body>
</html>
