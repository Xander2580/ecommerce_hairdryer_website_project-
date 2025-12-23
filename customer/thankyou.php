<?php
// thankyou.php
require_once '../config/database.php';
$conn = getDatabaseConnection();

$customer_id = $_SESSION['customer_id'] ?? null;
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Optional fallbacks from redirect/session
$pm_qs  = isset($_GET['pm']) ? strtolower(trim($_GET['pm'])) : '';
$pm_ses = isset($_SESSION['last_payment_method']) ? strtolower(trim($_SESSION['last_payment_method'])) : '';

$order = null;
if ($customer_id) {
    if ($order_id) {
        $sql = "SELECT * FROM orders WHERE order_id = $order_id AND customer_id = $customer_id LIMIT 1";
    } else {
        $sql = "SELECT * FROM orders WHERE customer_id = $customer_id ORDER BY created_at DESC LIMIT 1";
    }
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $order = $res->fetch_assoc();
    }
}

// Items
$order_items = [];
if ($order) {
    $oid = (int)$order['order_id'];
    $sql = "
        SELECT od.*, p.product_name, p.main_image
        FROM order_details od
        LEFT JOIN products p ON od.product_id = p.product_id
        WHERE od.order_id = $oid
    ";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) $order_items[] = $row;
}

// Resolve payment method with strong fallback chain
function resolve_pm($row_pm, $pm_qs, $pm_ses) {
    $pm = strtolower(trim($row_pm ?? ''));
    if ($pm === '') $pm = $pm_qs;
    if ($pm === '') $pm = $pm_ses;
    return $pm;
}
$resolved_pm = resolve_pm($order['payment_method'] ?? '', $pm_qs, $pm_ses);

function render_pm($pm) {
    if (in_array($pm, ['cod','cash on delivery','cash_on_delivery'], true)) return 'Cash on Delivery';
    if ($pm === 'esewa') return 'eSewa';
    if (in_array($pm, ['card','credit card','debit card','credit/debit card'], true)) return 'Credit/Debit Card';
    return $pm !== '' ? htmlspecialchars($pm) : 'Not specified';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Order Confirmed - StylePro Essentials</title>
  <link href="../assests/css/home.css" rel="stylesheet" />
  <link href="../assests/css/customerdashboard.css" rel="stylesheet" />
  <link href="../assests/css/profile.css" rel="stylesheet" />
</head>
<body data-page="thankyou">
  <?php include '../includes/navbar.php'; ?>

  <main>
    <div class="dashboard-header">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-md-12">
            <h1 class="display-6 fw-bold">Thank You!</h1>
            <p class="lead mb-0">
              <?= $order ? 'Your order has been placed successfully.' : 'No recent order found.' ?>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid my-4">
      <div class="row">
        <div class="col-lg-8 mb-4">
          <div class="card mb-4">
            <div class="card-body">
              <?php if ($order): ?>
                <h3 class="mb-2">Order <span class="text-primary">#<?= htmlspecialchars($order['order_id']) ?></span> confirmed</h3>
                <p class="text-muted mb-3">We’ve sent a confirmation email with your order details.</p>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <h6 class="mb-1">Order Date</h6>
                    <div><?= htmlspecialchars(date('Y-m-d', strtotime($order['created_at']))) ?></div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <h6 class="mb-1">Estimated Delivery</h6>
                    <div>3–5 business days</div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <h6 class="mb-1">Shipping To</h6>
                    <div><?= htmlspecialchars($order['shipping_address'] ?? 'No address provided') ?></div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <h6 class="mb-1">Payment Method</h6>
                    <div><?= render_pm($resolved_pm) ?></div>
                  </div>
                </div>
                <div class="d-flex" style="gap:12px;">
                  <a href="orders.php" class="btn btn-primary">View My Orders</a>
                  <a href="products.php" class="btn btn-light">Continue Shopping</a>
                </div>
              <?php else: ?>
                <div class="alert alert-warning">No recent order found.</div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Items -->
          <div class="card">
            <div class="card-header"><h5 class="mb-0">Items in this Order</h5></div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th style="width:120px;">Price</th>
                      <th style="width:100px;">Qty</th>
                      <th style="width:140px;">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($order_items): foreach ($order_items as $item): ?>
                      <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td>Rs. <?= number_format($item['price']) ?></td>
                        <td><?= (int)$item['quantity'] ?></td>
                        <td><strong>Rs. <?= number_format($item['price'] * $item['quantity']) ?></strong></td>
                      </tr>
                    <?php endforeach; else: ?>
                      <tr><td colspan="4" class="text-center text-muted">No items found for this order.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <?php if ($order): ?>
              <div class="d-flex justify-content-end mt-3" style="max-width:380px; margin-left:auto;">
                <div class="w-100">
                  <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span>Rs. <?= number_format($order['total_amount'] ?? 0) ?></span></div>
                  <div class="d-flex justify-content-between mb-1"><span>Shipping</span><span>Rs. <?= number_format($order['shipping_cost'] ?? 0) ?></span></div>
                  <hr>
                  <div class="d-flex justify-content-between"><strong>Total</strong><strong>Rs. <?= number_format($order['final_amount'] ?? 0) ?></strong></div>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Right column -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header"><h5 class="mb-0">What’s next?</h5></div>
            <div class="card-body">
              <ul class="list-unstyled mb-3">
                <li>• You’ll get another email when your order ships.</li>
                <li>• Track status from <a href="orders.php">My Orders</a>.</li>
              </ul>
              <a class="btn btn-light w-100 mb-2" href="orders.php">Go to My Orders</a>
              <a class="btn btn-primary w-100" href="products.php">Continue Shopping</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>
  <script src="../assets/js/include.js"></script>
</body>
</html>
