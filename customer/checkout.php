<?php
// checkout.php
require_once '../config/database.php';
$conn = getDatabaseConnection();

/* ---------- Load cart ---------- */
$cart_products = [];
$cart_total = 0.0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $res = $conn->query("SELECT * FROM products WHERE product_id IN ($ids)");
    while ($row = $res->fetch_assoc()) {
        $qty = (int)($_SESSION['cart'][$row['product_id']] ?? 0);
        $row['quantity'] = $qty;
        $row['subtotal'] = (float)$row['price'] * $qty;
        $cart_total += $row['subtotal'];
        $cart_products[] = $row;
    }
}
$default_shipping = 200;
$total = $cart_total + $default_shipping;

/* ---------- Place order ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customer_id = $_SESSION['customer_id'] ?? null;
    if (!$customer_id) {
        header('Location: ../auth/login.php');
        exit;
    }

    $shipping_address = trim(($_POST['address1'] ?? '') . ' ' . ($_POST['address2'] ?? ''));
    $billing_address  = $shipping_address;

    $order_number   = uniqid('ORD');
    $order_date     = date('Y-m-d H:i:s');

    // Payment & shipping selected by user
    $payment_method = isset($_POST['pay']) && $_POST['pay'] !== '' ? strtolower(trim($_POST['pay'])) : 'cod';
    $shipping_cost  = (isset($_POST['shipping']) && $_POST['shipping'] === 'exp') ? 400 : 200;

    // Persist the chosen method as a fallback for the thankyou page
    $_SESSION['last_payment_method'] = $payment_method;

    $payment_status = 'pending';
    $order_status   = 'pending';
    $tax_amount     = 0.0;
    $discount_amount= 0.0;
    $total_amount   = (float)$cart_total;
    $final_amount   = $total_amount + $shipping_cost;
    $notes          = '';

    // Escape all variables to prevent SQL injection
    $customer_id      = (int)$customer_id;
    $order_number     = $conn->real_escape_string($order_number);
    $order_date       = $conn->real_escape_string($order_date);
    $total_amount     = (float)$total_amount;
    $discount_amount  = (float)$discount_amount;
    $shipping_cost    = (float)$shipping_cost;
    $tax_amount       = (float)$tax_amount;
    $final_amount     = (float)$final_amount;
    $payment_method   = $conn->real_escape_string($payment_method);
    $payment_status   = $conn->real_escape_string($payment_status);
    $order_status     = $conn->real_escape_string($order_status);
    $shipping_address = $conn->real_escape_string($shipping_address);
    $billing_address  = $conn->real_escape_string($billing_address);
    $notes            = $conn->real_escape_string($notes);

    $sql = "
        INSERT INTO orders
        (customer_id, order_number, order_date, total_amount, discount_amount, shipping_cost, tax_amount, final_amount,
         payment_method, payment_status, order_status, shipping_address, billing_address, notes, created_at, updated_at)
        VALUES (
            $customer_id,
            '$order_number',
            '$order_date',
            $total_amount,
            $discount_amount,
            $shipping_cost,
            $tax_amount,
            $final_amount,
            '$payment_method',
            '$payment_status',
            '$order_status',
            '$shipping_address',
            '$billing_address',
            '$notes',
            NOW(),
            NOW()
        )
    ";
    $conn->query($sql);
    $order_id = $conn->insert_id;

    // Insert items
    if (!empty($_SESSION['cart'])) {
        $pstmt = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
        $istmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $product_id => $qty) {
            $pid = (int)$product_id;
            $pstmt->bind_param("i", $pid);
            $pstmt->execute();
            $pstmt->bind_result($price);
            $pstmt->fetch();
            $pstmt->free_result();

            $istmt->bind_param("iiid", $order_id, $pid, $qty, $price);
            $istmt->execute();
        }
        $pstmt->close();
        $istmt->close();
    }

    // Clear cart and go to thank you (also pass pm as explicit fallback)
    unset($_SESSION['cart']);
    header("Location: thankyou.php?order_id=".(int)$order_id."&pm=".urlencode($payment_method));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout - StylePro Essentials</title>
  <link href="../assests/css/home.css" rel="stylesheet" />
  <link href="../assests/css/customerdashboard.css" rel="stylesheet" />
  <link href="../assests/css/profile.css" rel="stylesheet" />
  <style>
    html, body { height: 100%; }
    body { display: flex; flex-direction: column; min-height: 100vh; }
    main { flex: 1; }
    footer.bg-dark.text-white { clear: both; width: 100%; }
  </style>
</head>
<body data-page="checkout">
  <?php include '../includes/navbar.php'; ?>

  <main>
    <div class="dashboard-header">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-md-12">
            <h1 class="display-6 fw-bold">Checkout</h1>
            <p class="lead mb-0">Enter your details and place your order</p>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid my-4">
      <div class="row">
        <!-- LEFT -->
        <div class="col-lg-8 mb-4">
          <!-- One form around EVERYTHING -->
          <form method="post" id="checkoutForm">
            <!-- Contact & Shipping -->
            <div class="card mb-4">
              <div class="card-header"><h5 class="mb-0">Contact & Shipping Information</h5></div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" class="form-control" name="full_name" required></div>
                  <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
                  <div class="col-md-6"><label class="form-label">Phone</label><input type="tel" class="form-control" name="phone"></div>
                  <div class="col-md-12"><label class="form-label">Address Line 1</label><input type="text" class="form-control" name="address1" required></div>
                  <div class="col-md-12"><label class="form-label">Address Line 2</label><input type="text" class="form-control" name="address2"></div>
                  <div class="col-md-4"><label class="form-label">City</label><input type="text" class="form-control" name="city" required></div>
                  <div class="col-md-4"><label class="form-label">State / Province</label><input type="text" class="form-control" name="state"></div>
                  <div class="col-md-4"><label class="form-label">Postal Code</label><input type="text" class="form-control" name="postal_code"></div>
                  <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select class="form-control" name="country">
                      <option selected>Nepal</option>
                      <option>India</option>
                      <option>Bangladesh</option>
                      <option>Sri Lanka</option>
                      <option>Other</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Delivery Method -->
            <div class="card mb-4">
              <div class="card-header"><h5 class="mb-0">Delivery Method</h5></div>
              <div class="card-body">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="shipping" id="std" value="std" checked>
                  <label class="form-check-label" for="std">Standard Shipping (3–5 days) — <strong>Rs. 200</strong></label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="shipping" id="exp" value="exp">
                  <label class="form-check-label" for="exp">Express Shipping (1–2 days) — <strong>Rs. 400</strong></label>
                </div>
              </div>
            </div>

            <!-- Payment -->
            <div class="card">
              <div class="card-header"><h5 class="mb-0">Payment</h5></div>
              <div class="card-body">
                <div class="form-check mb-3">
                  <input class="form-check-input" type="radio" name="pay" id="cod" value="cod" checked>
                  <label class="form-check-label" for="cod">Cash on Delivery</label>
                </div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="radio" name="pay" id="esewa" value="esewa">
                  <label class="form-check-label" for="esewa">eSewa Mobile Wallet</label>
                </div>
                <div id="esewa-info" style="display:none; background:#fff7e6; border:1px solid #ffa726; border-radius:6px; padding:18px; margin-bottom:12px;">
                  <p>You will be redirected to your eSewa account to complete your payment:</p>
                  <ol>
                    <li>Login with your eSewa ID and Password</li>
                    <li>Ensure sufficient balance</li>
                    <li>Enter the OTP</li>
                  </ol>
                  <button type="button" class="btn btn-warning" id="esewaPayBtn" style="background:#ff7800; color:#fff; border:none; margin-top:10px;">Pay Now</button>
                </div>

                <div class="form-check mb-3">
                  <input class="form-check-input" type="radio" name="pay" id="card" value="card">
                  <label class="form-check-label" for="card">Credit/Debit Card</label>
                </div>
                <div class="row g-3" id="card-info" style="display:none;">
                  <div class="col-md-8"><label class="form-label">Card Number</label><input type="text" class="form-control" placeholder="•••• •••• •••• ••••"></div>
                  <div class="col-md-2"><label class="form-label">MM/YY</label><input type="text" class="form-control" placeholder="MM/YY"></div>
                  <div class="col-md-2"><label class="form-label">CVV</label><input type="text" class="form-control" placeholder="CVV"></div>
                </div>

                <div class="form-check mt-3">
                  <input class="form-check-input" type="checkbox" id="agree" required>
                  <label class="form-check-label" for="agree">I agree to the <a href="#" class="text-muted">Terms & Conditions</a>.</label>
                </div>
                <div class="d-flex mt-4" style="gap:12px;">
                  <a href="cart.php" class="btn btn-light">Back to Cart</a>
                  <button type="submit" name="place_order" class="btn btn-primary" style="margin-left:auto;">Place Order</button>
                </div>
              </div>
            </div>
          </form>
          <!-- /form -->
        </div>

        <!-- RIGHT: summary -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header"><h5 class="mb-0">Order Summary</h5></div>
            <div class="card-body">
              <?php if ($cart_products): ?>
                <?php foreach ($cart_products as $item): ?>
                  <div class="d-flex justify-content-between mb-2">
                    <div>
                      <strong><?= htmlspecialchars($item['product_name']) ?></strong><br>
                      <small class="text-muted">Qty <?= (int)$item['quantity'] ?></small>
                    </div>
                    <div><strong><?= number_format($item['subtotal']) ?></strong></div>
                  </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong><?= number_format($cart_total) ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Shipping</span><strong data-shipping>Rs. <?= number_format($default_shipping) ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Tax</span><span class="text-muted">Included</span></div>
                <hr>
                <div class="d-flex justify-content-between mb-3"><strong>Total</strong><strong data-total>Rs. <?= number_format($total) ?></strong></div>
                <div class="d-flex" style="gap:12px;">
                  <a href="products.php" class="btn btn-light flex-fill text-nowrap">Continue Shopping</a>
                </div>
              <?php else: ?>
                <div class="alert alert-warning">Your cart is empty.</div>
              <?php endif; ?>
            </div>
          </div>

          <div class="card mt-3">
            <div class="card-header"><h6 class="mb-0">Need Help?</h6></div>
            <div class="card-body">
              <p class="mb-2">Have questions about your order?</p>
              <a href="contact.php" class="btn btn-light btn-sm">Contact Support</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>
  <script src="../assests/js/include.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Shipping UI math
    const stdRadio = document.getElementById('std');
    const expRadio = document.getElementById('exp');
    const shippingSpan = document.querySelector('strong[data-shipping]');
    const totalSpan = document.querySelector('strong[data-total]');
    const subtotal = <?= (float)$cart_total ?>;

    function updateTotal() {
      let shipping = stdRadio.checked ? 200 : 400;
      shippingSpan.textContent = 'Rs. ' + shipping;
      totalSpan.textContent = 'Rs. ' + (subtotal + shipping);
    }
    stdRadio.addEventListener('change', updateTotal);
    expRadio.addEventListener('change', updateTotal);

    // Payment UI toggles
    const esewaRadio = document.getElementById('esewa');
    const codRadio = document.getElementById('cod');
    const cardRadio = document.getElementById('card');
    const esewaInfo = document.getElementById('esewa-info');
    const cardInfo = document.getElementById('card-info');

    function toggleEsewaInfo() {
      if (esewaRadio.checked) {
        esewaInfo.style.display = 'block';
        cardInfo.style.display = 'none';
      } else {
        esewaInfo.style.display = 'none';
      }
    }
    function toggleCardInfo() {
      if (cardRadio.checked) {
        cardInfo.style.display = 'block';
        esewaInfo.style.display = 'none';
      } else {
        cardInfo.style.display = 'none';
      }
    }
    esewaRadio.addEventListener('change', toggleEsewaInfo);
    codRadio.addEventListener('change', toggleEsewaInfo);
    cardRadio.addEventListener('change', toggleCardInfo);

    // Optional direct eSewa flow (doesn't submit the checkout form)
    document.getElementById('esewaPayBtn').onclick = function() {
      const shipping = stdRadio.checked ? 200 : 400;
      const amount = (subtotal + shipping);
      window.location.href = 'esewa_pay.php?amount=' + amount;
    };
  });
  </script>
</body>
</html>
