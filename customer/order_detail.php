<?php
require_once '../config/database.php';
requireCustomerLogin();
$conn = getDatabaseConnection();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$customer_id = $_SESSION['customer_id'];

// Fetch order
$sql = "SELECT * FROM orders WHERE order_id = $order_id AND customer_id = $customer_id LIMIT 1";
$res = $conn->query($sql);
$order = $res && $res->num_rows > 0 ? $res->fetch_assoc() : null;

if (!$order) {
    // Order not found or not yours
    header("Location: orders.php");
    exit;
}

// Fetch order items
$order_items = [];
$sql = "SELECT od.*, p.product_name, p.main_image FROM order_details od
        LEFT JOIN products p ON od.product_id = p.product_id
        WHERE od.order_id = $order_id";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) $order_items[] = $row;

// Payment method display
function render_pm($pm) {
    $pm = strtolower(trim($pm));
    if ($pm === 'cod' || $pm === 'cash on delivery' || $pm === 'cash_on_delivery') return 'Cash on Delivery';
    if ($pm === 'esewa') return 'eSewa';
    if ($pm === 'card' || $pm === 'credit card' || $pm === 'debit card' || $pm === 'credit/debit card') return 'Credit/Debit Card';
    return $pm !== '' ? htmlspecialchars($pm) : 'Not specified';
}

// Status badge
function order_status_badge($status) {
    $status = strtolower($status);
    $badge = 'bg-secondary';
    if ($status === 'pending') $badge = 'bg-warning';
    elseif ($status === 'processing' || $status === 'confirmed') $badge = 'bg-info';
    elseif ($status === 'shipped') $badge = 'bg-primary';
    elseif ($status === 'delivered') $badge = 'bg-success';
    elseif ($status === 'cancelled') $badge = 'bg-danger';
    return '<span class="badge '.$badge.'">'.ucfirst($status).'</span>';
}

$customer = getCurrentCustomer();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Order #<?= htmlspecialchars($order['order_id']) ?> Details - StylePro Essentials</title>
  <link href="../assests/css/home.css" rel="stylesheet" />
  <link href="../assests/css/customerdashboard.css" rel="stylesheet" />
  <link href="../assests/css/profile.css" rel="stylesheet" />
</head>
<body data-page="order-detail">
  <?php include '../includes/navbar.php'; ?>
  <main>
    <div class="dashboard-header">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-md-12">
            <h1 class="display-6 fw-bold mb-1">Order <span class="text-primary">#<?= htmlspecialchars($order['order_id']) ?></span> Details</h1>
            <p class="lead mb-0">Review your order information and status</p>
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
          <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Order Summary</h5>
              <?= order_status_badge($order['order_status']) ?>
            </div>
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-6 mb-2">
                  <strong>Order Number:</strong> <?= htmlspecialchars($order['order_number']) ?><br>
                  <strong>Order Date:</strong> <?= formatDate($order['order_date'] ?? $order['created_at']) ?><br>
                  <strong>Payment Method:</strong> <?= render_pm($order['payment_method']) ?><br>
                  <strong>Tracking Number:</strong> <?= htmlspecialchars($order['tracking_number'] ?? '-') ?>
                </div>
                <div class="col-md-6 mb-2">
                  <strong>Shipping Address:</strong><br>
                  <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                  <?php if (!empty($order['notes'])): ?>
                    <br><strong>Notes:</strong> <?= nl2br(htmlspecialchars($order['notes'])) ?>
                  <?php endif; ?>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Price</th>
                      <th>Qty</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <?php if (!empty($item['main_image'])): ?>
                            <img src="../assests/images/products/<?= htmlspecialchars($item['main_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" style="width:48px; height:48px; object-fit:cover; border-radius:6px; margin-right:12px;">
                          <?php endif; ?>
                          <span><?= htmlspecialchars($item['product_name']) ?></span>
                        </div>
                      </td>
                      <td><?= formatPrice($item['price']) ?></td>
                      <td><?= (int)$item['quantity'] ?></td>
                      <td><strong><?= formatPrice($item['price'] * $item['quantity']) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <div class="d-flex justify-content-end mt-3" style="max-width:380px; margin-left:auto;">
                <div class="w-100">
                  <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span><?= formatPrice($order['total_amount']) ?></span></div>
                  <div class="d-flex justify-content-between mb-1"><span>Discount</span><span>-<?= formatPrice($order['discount_amount']) ?></span></div>
                  <div class="d-flex justify-content-between mb-1"><span>Shipping</span><span><?= formatPrice($order['shipping_cost']) ?></span></div>
                  <div class="d-flex justify-content-between mb-1"><span>Tax</span><span><?= formatPrice($order['tax_amount']) ?></span></div>
                  <hr>
                  <div class="d-flex justify-content-between"><strong>Total</strong><strong><?= formatPrice($order['final_amount']) ?></strong></div>
                </div>
              </div>
              <div class="mt-4">
                <a href="orders.php" class="btn btn-light">Back to My Orders</a>
                <a href="products.php" class="btn btn-primary">Continue Shopping</a>
              </div>
            </div>
          </div>
          <!-- Timeline / Status History (optional) -->
          <?php if (!empty($order['shipped_date']) || !empty($order['delivered_date'])): ?>
          <div class="card">
            <div class="card-header"><h6 class="mb-0">Order Timeline</h6></div>
            <div class="card-body">
              <ul class="timeline list-unstyled">
                <li>
                  <strong>Order Placed</strong>
                  <span class="text-muted ms-2"><?= formatDate($order['order_date'] ?? $order['created_at']) ?></span>
                </li>
                <?php if (!empty($order['shipped_date'])): ?>
                <li>
                  <strong>Shipped</strong>
                  <span class="text-muted ms-2"><?= formatDate($order['shipped_date']) ?></span>
                </li>
                <?php endif; ?>
                <?php if (!empty($order['delivered_date'])): ?>
                <li>
                  <strong>Delivered</strong>
                  <span class="text-muted ms-2"><?= formatDate($order['delivered_date']) ?></span>
                </li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>
  <?php include '../includes/footer.php'; ?>
  <script src="../assests/js/include.js"></script>
</body>
</html>