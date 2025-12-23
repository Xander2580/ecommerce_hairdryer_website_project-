  <?php
require_once '../config/database.php';
requireCustomerLogin();
$customer = getCurrentCustomer();
$conn = getDatabaseConnection();

// Fetch all orders for this customer
$customer_id = $_SESSION['customer_id'];
$orders = [];
$status = isset($_GET['status']) ? strtolower($_GET['status']) : '';
$where = "o.customer_id = $customer_id";
if ($status && in_array($status, ['pending','processing','shipped','delivered','cancelled','confirmed'])) {
    $where .= " AND LOWER(o.order_status) = '" . mysqli_real_escape_string($conn, $status) . "'";
}
$sql = "SELECT o.*, GROUP_CONCAT(DISTINCT p.product_name SEPARATOR ', ') as products, SUM(od.quantity) as total_items
        FROM orders o
        LEFT JOIN order_details od ON o.order_id = od.order_id
        LEFT JOIN products p ON od.product_id = p.product_id
        WHERE $where
        GROUP BY o.order_id
        ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Orders - StylePro Essentials</title>
  <link href="../assests/css/home.css" rel="stylesheet" />
  <link href="../assests/css/customerdashboard.css" rel="stylesheet" />
  <link href="../assests/css/profile.css" rel="stylesheet" />
</head>
<body data-page="orders">
  <!-- Navigation Include -->
  <?php include '../includes/navbar.php'; ?>
  <main>
    <!-- Page header -->
    <div class="dashboard-header">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-md-12">
            <h1 class="display-6 fw-bold">My Orders</h1>
            <p class="lead mb-0">Track your purchases and view order details</p>
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
              <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
              <li class="nav-item"><a class="nav-link" href="profile.php">My Profile</a></li>
              <li class="nav-item"><a class="nav-link active" href="orders.php">My Orders</a></li>
              <li class="nav-item"><a class="nav-link" href="products.php">Browse Products</a></li>
              <li class="nav-item"><a class="nav-link" href="cart.php">Shopping Cart</a></li>
              <li class="nav-item"><a class="nav-link" href="../index.php">Back to Store</a></li>
            </ul>
          </div>
        </div>
        <!-- Main content -->
        <div class="col-lg-9">
          <!-- Filters / actions -->
          <div class="card mb-3">
            <div class="card-header">
              <h5 class="mb-0">Orders</h5>
              <div></div>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex" style="gap:8px; flex-wrap:wrap;">
                  <a href="#" class="btn btn-light btn-sm">All</a>
                  <a href="orders.php?status=pending" class="btn btn-light btn-sm<?= (isset($_GET['status']) && $_GET['status'] == 'pending' ? ' active' : '') ?>">Pending</a>
                  <a href="#" class="btn btn-light btn-sm">Processing</a>
                  <a href="#" class="btn btn-light btn-sm">Shipped</a>
                  <a href="#" class="btn btn-light btn-sm">Delivered</a>
                  <a href="#" class="btn btn-light btn-sm">Cancelled</a>
                </div>
                <div>
                  <input type="text" class="form-control" placeholder="Search orders..." style="min-width:220px;">
                </div>
              </div>
              <!-- Orders table -->
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
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                      <td><strong class="text-primary">#<?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                      <td>
                        <small class="text-muted">
                          <?php echo htmlspecialchars($order['products']); ?>
                        </small>
                      </td>
                      <td><span class="badge bg-secondary"><?php echo (int)$order['total_items']; ?></span></td>
                      <td><strong class="text-success"><?php echo formatPrice($order['final_amount']); ?></strong></td>
                      <td>
                        <?php
                          $status = strtolower($order['order_status']);
                          $badge = 'bg-secondary';
                          if ($status === 'pending') $badge = 'bg-warning';
                          elseif ($status === 'processing' || $status === 'confirmed') $badge = 'bg-info';
                          elseif ($status === 'shipped') $badge = 'bg-primary';
                          elseif ($status === 'delivered') $badge = 'bg-success';
                          elseif ($status === 'cancelled') $badge = 'bg-danger';
                        ?>
                        <span class="badge <?php echo $badge; ?>">
                          <?php echo ucfirst($order['order_status']); ?>
                        </span>
                      </td>
                      <td><small><?php echo formatDate($order['created_at']); ?></small></td>
                      <td>
                        <a href="order_detail.php?id=<?php echo urlencode($order['order_id']); ?>" class="btn btn-light btn-sm">View</a>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted">No orders found.</td></tr>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <!-- Pagination (static) -->
              <nav class="mt-3">
                <ul class="pagination">
                  <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
                  <li class="page-item active"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <!-- Footer Include -->
  <?php include '../includes/footer.php'; ?>
  <script src="../assests/js/include.js"></script>
</body>
</html>
