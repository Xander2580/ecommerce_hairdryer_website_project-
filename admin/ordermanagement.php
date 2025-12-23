<?php
// admin/ordermanagement.php - Manage Orders
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

// --- DELETE ORDER ---
if (isset($_GET['delete_id'])) {
    $order_id = (int) $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $_SESSION['msg'] = $stmt->execute() ? "Order deleted successfully." : "Order deletion failed.";
    header("Location: ordermanagement.php");
    exit();
}

// --- UPDATE STATUS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $order_id = (int) $_POST['order_id'];
    $order_status = $_POST['order_status'] ?? 'pending';
    $stmt = $conn->prepare("UPDATE orders SET order_status=? WHERE order_id=?");
    $stmt->bind_param("si", $order_status, $order_id);
    $_SESSION['msg'] = $stmt->execute() ? "Order status updated successfully." : "Order status update failed.";
    header("Location: ordermanagement.php");
    exit();
}

/* ==========================
   FILTERS (GET)
   - status: all|pending|confirmed|shipped|delivered|cancelled
   - q: search (order id, customer name, email)
   - date_from, date_to (YYYY-MM-DD)
   ========================== */
$valid_statuses = ['all','pending','confirmed','shipped','delivered','cancelled'];
$status    = strtolower(trim($_GET['status'] ?? 'all'));
$status    = in_array($status, $valid_statuses, true) ? $status : 'all';
$q         = trim($_GET['q'] ?? '');
$date_from = trim($_GET['date_from'] ?? '');
$date_to   = trim($_GET['date_to'] ?? '');

// Build WHERE dynamically
$where = [];
$params = [];
$types  = "";

// Status
if ($status !== 'all') {
    $where[] = "o.order_status = ?";
    $params[] = $status;
    $types   .= "s";
}

// Search q (order id OR customer name OR email)
if ($q !== '') {
    // If q is numeric, try order_id match; else name/email like
    if (ctype_digit($q)) {
        $where[] = "o.order_id = ?";
        $params[] = (int)$q;
        $types   .= "i";
    } else {
        $like = "%$q%";
        $where[] = "(CONCAT(c.first_name, ' ', c.last_name) LIKE ? OR c.email LIKE ?)";
        $params[] = $like; $params[] = $like;
        $types   .= "ss";
    }
}

// Date range
// Normalize to whole days; created_at is assumed DATETIME
if ($date_from !== '') {
    $where[] = "DATE(o.created_at) >= ?";
    $params[] = $date_from;
    $types   .= "s";
}
if ($date_to !== '') {
    $where[] = "DATE(o.created_at) <= ?";
    $params[] = $date_to;
    $types   .= "s";
}

$sql = "SELECT o.*, c.first_name, c.last_name, c.email
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY o.created_at DESC";

// Prepare and execute
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders_result = $stmt->get_result();

// Badge color helper
function badgeClass($status) {
    return match (strtolower($status)) {
        'pending'    => 'bg-warning',
        'confirmed'  => 'bg-primary',
        'shipped'    => 'bg-info',
        'delivered'  => 'bg-success',
        'cancelled'  => 'bg-danger',
        default      => 'bg-secondary'
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Hair Care Store</title>
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/admindashboard.css" rel="stylesheet">
    <style>
        /* Small polish for the filter bar */
        .filter-bar {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
            gap: 12px;
            align-items: end;
            margin-bottom: 16px;
        }
        .filter-bar .form-group label {
            display:block;
            margin-bottom:6px;
            font-weight:600;
            color:#2c3e50;
        }
        .filter-bar .form-group input,
        .filter-bar .form-group select {
            width:100%;
            padding:10px 12px;
            border:1px solid #e4e8ee;
            border-radius:10px;
            background:#fbfcfe;
        }
        @media (max-width: 1100px) {
            .filter-bar { grid-template-columns: 1fr 1fr; }
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
            <li><a class="nav-link active" href="ordermanagement.php">Order Management</a></li>
            <li><a class="nav-link" href="customermanagement.php">Customer Management</a></li>
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
                <h1>Order Management</h1>
                <p>Manage and update customer orders</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>All Orders</h6>
            </div>
            <div class="card-body">

                <!-- Filter/Search Bar -->
                <form class="filter-bar" method="GET" action="ordermanagement.php">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <?php
                            foreach ($valid_statuses as $s) {
                                $label = ucfirst($s);
                                $sel = ($status === $s) ? 'selected' : '';
                                echo "<option value=\"$s\" $sel>$label</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="q">Search (Order # / Name / Email)</label>
                        <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="e.g., 1023 or john or john@email.com">
                    </div>
                    <div class="form-group">
                        <label for="date_from">From (Date)</label>
                        <input type="date" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>
                    <div class="form-group">
                        <label for="date_to">To (Date)</label>
                        <input type="date" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                    <div class="form-group" style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="ordermanagement.php" class="btn btn-outline-success">Reset</a>
                    </div>
                </form>

                <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo (int)$order['order_id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['email']); ?></td>
                                <td><strong><?php echo 'Rs ' . number_format((float)$order['final_amount'], 2); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo badgeClass($order['order_status']); ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($order['created_at']))); ?></td>
                                <td>
                                    <form method="POST" style="display:inline-flex;gap:6px;align-items:center;">
                                        <input type="hidden" name="order_id" value="<?php echo (int)$order['order_id']; ?>">
                                        <select name="order_status" required>
                                            <?php
                                            $statuses = ['pending','confirmed','shipped','delivered','cancelled'];
                                            foreach ($statuses as $s) {
                                                $sel = ($order['order_status'] === $s) ? 'selected' : '';
                                                echo "<option value=\"$s\" $sel>" . ucfirst($s) . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" name="update_order" class="btn btn-success btn-sm">Update</button>
                                    </form>
                                    <a href="?delete_id=<?php echo (int)$order['order_id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-4">
                        <h5 class="text-muted">No orders found</h5>
                        <p class="text-muted">Try adjusting filters or date range.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- /.admin-main-content -->
</div><!-- /.container-fluid -->
</body>
</html>
