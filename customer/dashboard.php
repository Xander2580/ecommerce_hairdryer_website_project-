<?php
require_once '../config/database.php';
requireCustomerLogin();

$customer = getCurrentCustomer();
$conn = getDatabaseConnection();

// Get customer statistics
try {
    // Total orders
    $customer_id = $_SESSION['customer_id'];
    $sql = "SELECT COUNT(*) as total FROM orders WHERE customer_id = $customer_id";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $total_orders = $data['total'];

    // Total spent
    $sql = "SELECT SUM(final_amount) as total FROM orders WHERE customer_id = $customer_id AND payment_status = 'paid'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $total_spent = $data['total'] ?? 0;

    // Pending orders
    $sql = "SELECT COUNT(*) as total FROM orders WHERE customer_id = $customer_id AND order_status IN ('pending', 'confirmed', 'processing')";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $pending_orders = $data['total'];

    // Recent orders with product details 
    $sql = "SELECT o.*, 
                   GROUP_CONCAT(DISTINCT p.product_name SEPARATOR ', ') as products,
                   SUM(od.quantity) as total_items
            FROM orders o 
            LEFT JOIN order_details od ON o.order_id = od.order_id
            LEFT JOIN products p ON od.product_id = p.product_id
            WHERE o.customer_id = $customer_id 
            GROUP BY o.order_id
            ORDER BY o.created_at DESC 
            LIMIT 5";
    $recent_orders_result = mysqli_query($conn, $sql);
    $recent_orders = [];
    while ($row = mysqli_fetch_assoc($recent_orders_result)) {
        $recent_orders[] = $row;
    }

    // Get featured products for recommendations 
    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.status = 'active' AND p.is_featured = 1 
            ORDER BY RAND() 
            LIMIT 2";
    $recommended_products_result = mysqli_query($conn, $sql);
    $recommended_products = [];
    while ($row = mysqli_fetch_assoc($recommended_products_result)) {
        $recommended_products[] = $row;
    }

} catch(Exception $e) {
    $error_message = "Error: " . $e->getMessage();
    echo '<div style="color:red; padding:10px;">' . $error_message . '</div>';
}

// Greeting based on time
date_default_timezone_set('Asia/Kathmandu'); // Set your timezone
$hour = (int)date('H');
if ($hour >= 5 && $hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour >= 12 && $hour < 17) {
    $greeting = "Good Afternoon";
} elseif ($hour >= 17 && $hour < 21) {
    $greeting = "Good Evening";
} else {
    $greeting = "Good Night";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StylePro Essentials</title>
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/customerdashboard.css" rel="stylesheet">
    <link href="../assests/css/profile.css" rel="stylesheet">
</head>
<body data-page="dashboard">
    <!-- Navigation Include -->
    <div data-include="../includes/navbar.php"></div>
    
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <h1 class="display-6 fw-bold">
                        Welcome<?php if (!empty($customer['first_name'])) { echo ', ' . htmlspecialchars($customer['first_name']); } ?>!
                    </h1>
                    <p class="lead mb-0">Manage your orders, profile, and discover new hair care products</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid my-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="sidebar-nav">
                    <div class="text-center mb-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: white; font-size: 1.5rem;">
                            <?= strtoupper(substr($customer['first_name'],0,1).substr($customer['last_name'],0,1)) ?>
                        </div>
                        <h5 class="mt-2 mb-0"><?= htmlspecialchars($customer['first_name'].' '.$customer['last_name']) ?></h5>
                        <small class="text-muted"><?= htmlspecialchars($customer['email']) ?></small>
                    </div>
                    
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
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
                <!-- Welcome Card -->
                <div class="welcome-card mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-primary mb-2">
                                <?php echo $greeting; ?><?php if (!empty($customer['first_name'])) { echo ', ' . htmlspecialchars($customer['first_name']); } ?>!
                            </h3>
                            <p class="mb-0 text-muted">Ready to discover some amazing hair care products? Check out our latest arrivals and special offers.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="products.php" class="btn btn-primary btn-lg">
                                Start Shopping
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card stat-card h-100 fade-in">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h3 class="mb-0 fw-bold"><?= $total_orders ?></h3>
                                        <p class="text-muted mb-0 small">Total Orders</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card stat-card h-100 fade-in">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h3 class="mb-0 fw-bold">Rs. <?= number_format($total_spent) ?></h3>
                                        <p class="text-muted mb-0 small">Total Spent</p>
                                        <small class="text-success">Lifetime value</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card stat-card h-100 fade-in">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h3 class="mb-0 fw-bold"><?= $pending_orders ?></h3>
                                        <p class="text-muted mb-0 small">Pending Orders</p>
                                        <small class="text-info">Awaiting delivery</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <a href="products.php" class="quick-action-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag-icon lucide-shopping-bag"><path d="M16 10a4 4 0 0 1-8 0"/><path d="M3.103 6.034h17.794"/><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"/></svg>
                                    Shop Products
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="orders.php" class="quick-action-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-search-icon lucide-file-search"><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M4.268 21a2 2 0 0 0 1.727 1H18a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v3"/><path d="m9 18-1.5-1.5"/><circle cx="5" cy="14" r="3"/></svg>
                                    View Orders
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="profile.php" class="quick-action-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-pen-icon lucide-user-pen"><path d="M11.5 15H7a4 4 0 0 0-4 4v2"/><path d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z"/><circle cx="10" cy="7" r="4"/></svg>
                                    Edit Profile
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="cart.php" class="quick-action-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart-icon lucide-shopping-cart"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                    My Cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Orders</h5>
                                <a href="orders.php" class="btn btn-light btn-sm">View All Orders</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Products</th>
                                                <th>Items</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_orders)): ?>
                                                <?php foreach ($recent_orders as $order): ?>
                                                    <tr>
                                                        <td>
                                                            <strong class="text-primary">#ORD<?= htmlspecialchars($order['order_id']) ?></strong>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted" title="<?= htmlspecialchars($order['products']) ?>">
                                                                <?= htmlspecialchars($order['products']) ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary"><?= (int)$order['total_items'] ?> item<?= ((int)$order['total_items'] > 1 ? 's' : '') ?></span>
                                                        </td>
                                                        <td>
                                                            <strong class="text-success">Rs. <?= number_format($order['final_amount']) ?></strong>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $status = strtolower($order['order_status']);
                                                            $badge = 'secondary';
                                                            if ($status === 'pending') $badge = 'warning';
                                                            elseif ($status === 'processing' || $status === 'confirmed') $badge = 'info';
                                                            elseif ($status === 'delivered') $badge = 'success';
                                                            elseif ($status === 'cancelled') $badge = 'danger';
                                                            ?>
                                                            <span class="badge bg-<?= $badge ?>">
                                                                <?= ucfirst($status) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small><?= htmlspecialchars(date('Y-m-d', strtotime($order['created_at']))) ?></small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No recent orders found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Products -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recommended for You</h5>
                            </div>
                            <div class="card-body p-2">
                                <?php if (!empty($recommended_products)): ?>
                                    <?php foreach ($recommended_products as $product): ?>
                                        <div class="card product-recommendation mb-2">
                                            <div class="row g-0">
                                                <div class="col-4">
                                                    <img src="../assests/images/products/<?= htmlspecialchars($product['main_image']) ?>" class="img-fluid" alt="<?= htmlspecialchars($product['product_name']) ?>" loading="lazy">
                                                </div>
                                                <div class="col-8">
                                                    <div class="card-body p-2">
                                                        <h6 class="card-title mb-1 small"><?= htmlspecialchars($product['product_name']) ?></h6>
                                                        <p class="card-text mb-1">
                                                            <small class="text-success fw-bold">Rs. <?= number_format($product['price']) ?></small>
                                                        </p>
                                                        <a href="product_detail.php?id=<?= $product['product_id'] ?>" class="btn btn-primary btn-sm">
                                                            View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-muted text-center">No recommendations at this time.</div>
                                <?php endif; ?>
                                <div class="text-center mt-2">
                                    <a href="products.php" class="btn btn-outline-success btn-sm">
                                        View All Products
                                    </a>
                                </div>
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