<?php
require_once '../config/database.php';
$conn = getDatabaseConnection();

if (isset($_POST['buy_now'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][$product_id] = $quantity; // Set only this product/qty
    header("Location: cart.php");
    exit();
}
// Add to cart logic for POST (from product_detail.php)
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    // Redirect to cart to prevent resubmission
    header("Location: cart.php");
    exit();
}

// Add to cart logic
if (isset($_GET['add'])) {
    $product_id = (int)$_GET['add'];
    // Initialize cart if not set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    // If product already in cart, increase quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    // Redirect to cart page without add param to prevent duplicate add on refresh
    header("Location: cart.php");
    exit();
}

// Remove from cart logic
if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: cart.php");
    exit();
}

// Update cart quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $product_id => $qty) {
        $qty = max(1, (int)$qty);
        $_SESSION['cart'][$product_id] = $qty;
    }
    header("Location: cart.php");
    exit();
}

// Clear cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

// Fetch cart products from DB
$cart_products = [];
$cart_total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $sql = "SELECT * FROM products WHERE product_id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $_SESSION['cart'][$row['product_id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $cart_total += $row['subtotal'];
        $cart_products[] = $row;
    }
}

// Fetch suggested products (exclude products already in cart)
$suggested_products = [];
$exclude_ids = !empty($_SESSION['cart']) ? implode(',', array_map('intval', array_keys($_SESSION['cart']))) : '0';
$suggest_sql = "SELECT * FROM products WHERE status = 1 AND product_id NOT IN ($exclude_ids) ORDER BY RAND() LIMIT 1";
$suggest_result = $conn->query($suggest_sql);
while ($row = $suggest_result->fetch_assoc()) {
    $suggested_products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shopping Cart - StylePro Essentials</title>
  <link href="../assests/css/home.css" rel="stylesheet" />
  <link href="../assests/css/customerdashboard.css" rel="stylesheet" />
  <link href="../assests/css/profile.css" rel="stylesheet" />
</head>
<body data-page="cart">
  <!-- Navigation Include -->
  <?php include '../includes/navbar.php'; ?>

  <main>
    <!-- Header -->
    <div class="dashboard-header">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-md-12">
            <h1 class="display-6 fw-bold">Shopping Cart</h1>
            <p class="lead mb-0">Review your items and proceed to checkout</p>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid my-4">
      <div class="row">
        <!-- Cart items -->
        <div class="col-lg-8 mb-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Your Items</h5>
            </div>
            <div class="card-body">
              <!-- Cart Table -->
              <div class="table-responsive">
                <form method="post">
                <table class="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th style="width:80px;">Image</th>
                      <th>Product</th>
                      <th style="width:120px;">Price</th>
                      <th style="width:120px;">Quantity</th>
                      <th style="width:120px;">Subtotal</th>
                      <th style="width:100px;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="cart-table-body">
                    <?php if ($cart_products): foreach ($cart_products as $item): ?>
                    <tr>
                      <td>
                        <img src="../assests/images/products/<?= htmlspecialchars($item['main_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" style="width:64px; height:auto;">
                      </td>
                      <td>
                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                        <div><small class="text-muted"><?= htmlspecialchars($item['description']) ?></small></div>
                      </td>
                      <td><strong class="text-success"><?= formatPrice($item['price']) ?></strong></td>
                      <td>
                        <input type="number" class="form-control" min="1" name="qty[<?= $item['product_id'] ?>]" value="<?= $item['quantity'] ?>">
                      </td>
                      <td><strong><?= formatPrice($item['subtotal']) ?></strong></td>
                      <td>
                        <a href="cart.php?remove=<?= $item['product_id'] ?>" class="btn btn-light btn-sm">Remove</a>
                      </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6">Your cart is empty.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
                <div class="mt-4 d-flex" style="gap:12px;">
                  <a href="products.php" class="btn btn-light">Continue Shopping</a>
                  <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                  <a href="cart.php?clear=1" class="btn btn-secondary" style="margin-left:auto;">Clear Cart</a>
                </div>
                </form>
              </div>

              <!-- Cart actions with proper spacing -->
              
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span>Subtotal</span>
                <strong id="cart-subtotal"><?= formatPrice($cart_total) ?></strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span>Shipping</span>
                <span class="text-muted">Calculated at checkout</span>
              </div>
              <hr>
              <div class="d-flex justify-content-between mb-3">
                <strong>Total</strong>
                <strong id="cart-total"><?= formatPrice($cart_total) ?></strong>
              </div>

              <!-- Updated buttons with better sizing and spacing -->
              <div class="d-flex" style="gap: 0.75rem;">
                <a href="products.php" class="btn btn-light" style="flex: 1; white-space: nowrap; font-size: 0.8rem; padding: 0.6rem 0.5rem;">Back to Products</a>                
                <a href="checkout.php" class="btn btn-primary" style="flex: 1; white-space: nowrap; font-size: 0.8rem; padding: 0.6rem 0.5rem;">Proceed to Checkout</a>
              </div>
            </div>
          </div>

          <!-- You might also like (dynamic) -->
          <div class="card mt-3">
            <div class="card-header">
              <h6 class="mb-0">You Might Also Like</h6>
            </div>
            <div class="card-body p-2">
              <div class="row g-2">
                <?php foreach ($suggested_products as $suggest): ?>
                <div class="col-6">
                  <div class="card h-100">
                    <img src="../assests/images/products/<?= htmlspecialchars($suggest['main_image']) ?>" class="img-fluid" alt="<?= htmlspecialchars($suggest['product_name']) ?>" loading="lazy">
                    <div class="p-2">
                      <small><?= htmlspecialchars($suggest['product_name']) ?></small><br>
                      <small class="text-success fw-bold"><?= formatPrice($suggest['price']) ?></small>
                    </div>
                    <div class="p-2">
                      <a href="product_detail.php?id=<?= $suggest['product_id'] ?>" class="btn btn-light btn-sm">View</a>
                      <button type="button" class="btn btn-primary btn-sm add-to-cart-btn" data-id="<?= $suggest['product_id'] ?>">Add to Cart</button>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($suggested_products)): ?>
                <div class="col-12"><small>No suggestions available.</small></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /row -->
    </div><!-- /container -->
  </main>

  <!-- Footer Include -->
  <?php include '../includes/footer.php'; ?>
  <script src="../assests/js/include.js"></script>

  <!-- Success Modal -->
<div id="cartModal" style="display:none; position:fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; padding:30px 40px; border-radius:8px; box-shadow:0 2px 12px #0002; text-align:center;">
    <h5 style="margin-bottom:16px;">Product successfully added to the cart!</h5>
    <button onclick="document.getElementById('cartModal').style.display='none'" class="btn btn-primary">OK</button>
  </div>
</div>
<script>  
function updateCartTable() {
  fetch('ajax_cart_table.php')
    .then(res => res.json())
    .then(data => {
      document.getElementById('cart-table-body').innerHTML = data.html;
      document.getElementById('cart-subtotal').textContent = data.cart_total;
      document.getElementById('cart-total').textContent = data.cart_total;
    });
}

document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var productId = this.getAttribute('data-id');
    fetch('ajax_add_to_cart.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'product_id=' + encodeURIComponent(productId) + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('cartModal').style.display = 'flex';
        // Update cart count in navbar
        fetch('ajax_cart_count.php')
          .then(res => res.json())
          .then(countData => {
            var badge = document.getElementById('cart-count');
            if (badge) badge.textContent = countData.count;
          });
        // Update cart table and totals
        updateCartTable();
      }
    });
  });
});
</script>
</body>
</html>
