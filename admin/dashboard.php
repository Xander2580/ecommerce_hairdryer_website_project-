<?php
// admin/dashboard.php - Admin Main Dashboard
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';
requireAdminLogin();

$user = getCurrentUser();

// Get database connection
$conn = getDatabaseConnection();

// Helpers
function badgeClassForStatus($status) {
    $status = strtolower(trim((string)$status));
    return match ($status) {
        'paid', 'completed', 'success', 'delivered' => 'bg-success',
        'pending', 'processing'                    => 'bg-primary',
        'failed', 'cancelled', 'canceled', 'refunded' => 'bg-danger',
        'on hold', 'hold'                          => 'bg-secondary',
        default                                    => 'bg-warning'
    };
}

try {
    // Total products
    $sql = "SELECT COUNT(*) AS total FROM products WHERE status = 'active'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $total_products = (int)($data['total'] ?? 0);

    // Total customers
    $sql = "SELECT COUNT(*) AS total FROM customers WHERE status = 'active'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $total_customers = (int)($data['total'] ?? 0);

    // Total orders
    $sql = "SELECT COUNT(*) AS total FROM orders";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $total_orders = (int)($data['total'] ?? 0);

    // Total revenue (paid orders)
    $sql = "SELECT COALESCE(SUM(final_amount),0) AS total FROM orders WHERE payment_status = 'paid'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $total_revenue = (float)($data['total'] ?? 0);

    // Recent orders
    $sql = "SELECT o.*, CONCAT(c.first_name, ' ', c.last_name) AS customer_name 
            FROM orders o 
            JOIN customers c ON o.customer_id = c.customer_id 
            ORDER BY o.created_at DESC 
            LIMIT 5";
    $recent_orders_result = mysqli_query($conn, $sql);
    $recent_orders = [];
    if ($recent_orders_result) {
        while ($row = mysqli_fetch_assoc($recent_orders_result)) {
            $recent_orders[] = $row;
        }
    }

    // Low stock products
    $sql = "SELECT product_id, product_name, stock_quantity 
            FROM products 
            WHERE stock_quantity <= 5 AND status = 'active' 
            ORDER BY stock_quantity ASC 
            LIMIT 5";
    $low_stock_result = mysqli_query($conn, $sql);
    $low_stock_products = [];
    if ($low_stock_result) {
        while ($row = mysqli_fetch_assoc($low_stock_result)) {
            $low_stock_products[] = $row;
        }
    }
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Dashboard - Hair Care Store</title>
    <!-- Keep paths exactly as your project uses them -->
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/admindashboard.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="text-center">
                <div style="width:60px;height:60px;background:#007bff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;"></div>
                <h6>Admin Panel</h6>
                <small class="text-muted">Welcome, <?php echo htmlspecialchars($user['name'] ?? $user['username'] ?? 'Admin'); ?></small>
            </div>
            <ul class="nav">
                <li><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li><a class="nav-link" href="productmanagement.php">Product Management</a></li>
                <li><a class="nav-link" href="ordermanagement.php">Order Management</a></li>
                <li><a class="nav-link" href="customermanagement.php">Customer Management</a></li>
                <li><a class="nav-link" href="usermanagement.php">User Management</a></li>
                <li><a class="nav-link" href="report&analytics.php">Reports &amp; Analytics</a></li>
                <li><a class="nav-link" href="../index.php" target="_blank">View Website</a></li>
                <li><a class="nav-link text-danger" href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="admin-main-content">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Hair Care Store Management System</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="stat-card">
                    <div>
                        <div class="text-uppercase">Total Products</div>
                        <div class="h5"><?php echo number_format($total_products); ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div>
                        <div class="text-uppercase">Total Customers</div>
                        <div class="h5"><?php echo number_format($total_customers); ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div>
                        <div class="text-uppercase">Total Orders</div>
                        <div class="h5"><?php echo number_format($total_orders); ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div>
                        <div class="text-uppercase">Total Revenue</div>
                        <div class="h5">
                            <?php
                                // Format with 2 decimals; change currency symbol as needed.
                                echo 'Rs ' . number_format($total_revenue, 2);
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:25px;">
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h6>Recent Orders</h6>
                        <a href="ordermanagement.php" class="btn btn-primary btn-sm">View All Orders</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_orders)): ?>
                            <div class="text-center" style="padding:32px 0;">
                                <h5 class="text-muted">No orders yet</h5>
                                <p class="text-muted">Orders will appear here once customers start purchasing.</p>
                            </div>
                        <?php else: ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td><strong><?php echo 'Rs ' . number_format((float)$order['final_amount'], 2); ?></strong></td>
                                            <td>
                                                <?php
                                                    $badge = badgeClassForStatus($order['payment_status'] ?: $order['status'] ?? '');
                                                    $label = ucfirst($order['payment_status'] ?: ($order['status'] ?? ''));
                                                ?>
                                                <span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($label ?: '—'); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($order['created_at']))); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="card">
                    <div class="card-header bg-warning">
                        <h6>Low Stock Alert</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($low_stock_products)): ?>
                            <div class="text-center" style="padding:32px 0;">
                                <p class="text-success">All products are well stocked!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($low_stock_products as $p): ?>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;padding:12px;border:1px solid #e9ecef;border-radius:8px;background:#fff;">
                                    <div style="flex:1;">
                                        <div style="font-weight:600;"><?php echo htmlspecialchars($p['product_name']); ?></div>
                                        <small class="text-muted">Stock Level</small>
                                    </div>
                                    <span class="badge bg-warning"><?php echo (int)$p['stock_quantity']; ?> left</span>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center" style="margin-top:15px;">
                                <a href="productmanagement.php" class="btn btn-warning btn-sm">Restock Products</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div><!-- /.admin-main-content -->
    </div><!-- /.container-fluid -->
</body>
</html>
