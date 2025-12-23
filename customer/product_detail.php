<?php
require_once '../config/database.php';
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?redirect=product_detail.php?id=" . urlencode($_GET['id']));
    exit;
}
$conn = getDatabaseConnection();
$product = null;


if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = '$id' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Details - StylePro Essentials</title>
  <link href="../assests/css/home.css" rel="stylesheet"/>
  <link href="../assests/css/customerdashboard.css" rel="stylesheet"/>
  <link href="../assests/css/productDetails.css" rel="stylesheet"/>
</head>
<body>
  <!-- Navbar include -->
  <?php include '../includes/navbar.php'; ?>
  <main class="container-fluid my-4">
    <div class="breadcrumbs">
      <a href="../index.php">Home</a> &rsaquo; <a href="products.php">Products</a> &rsaquo; <span><?php echo $product ? htmlspecialchars($product['product_name']) : 'Product Not Found'; ?></span>
    </div>
    <?php if ($product): ?>
    <div class="pd-wrap">
      <!-- Left: Gallery -->
      <div class="pd-left"> 
        <div class="pd-gallery">
          <div class="pd-hero">
            <?php if (!empty($product['main_image'])): ?>
              <img src="../assests/images/products/<?php echo htmlspecialchars($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="max-width:100%;height:auto;">
            <?php else: ?>
              <img src="../assests/images/no-image.png" alt="No image available" style="max-width:100%;height:auto;">
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- Right: Buy box -->
      <div class="pd-right">
        <div class="pd-card">
          <div class="pd-body">
            <h2 class="pd-title"><?php echo htmlspecialchars($product['product_name']); ?></h2>
            <div class="pd-sub"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></div>
            <div class="pd-rating">
              <div class="stars">★★★★★</div>
              <small class="text-muted">(0 reviews)</small>
              <span class="stock-badge <?php echo ($product['stock_quantity'] > 0) ? 'stock-in' : 'stock-out'; ?>">
                <?php echo ($product['stock_quantity'] > 0) ? 'In Stock' : 'Out of Stock'; ?>
              </span>
            </div>
            <div class="pd-price">
              <span class="price"><?php echo formatPrice($product['price']); ?></span>
              <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                <span class="old-price"><?php echo formatPrice($product['old_price']); ?></span>
                <span class="save-pill">Save <?php echo formatPrice($product['old_price'] - $product['price']); ?></span>
              <?php endif; ?>
            </div>
            <ul class="pd-feats">
              <?php if (!empty($product['features'])):
                foreach (explode("|", $product['features']) as $feat): ?>
                  <li><?php echo htmlspecialchars($feat); ?></li>
                <?php endforeach;
              endif; ?>
            </ul>
            <form method="post" action="cart.php">
              <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
              <div class="qty-wrap">
                <button type="button" class="qty-btn" onclick="var q=document.getElementById('qty');if(q.value>1)q.value--">−</button>
                <input type="number" class="qty" id="qty" name="quantity" value="1" min="1">
                <button type="button" class="qty-btn" onclick="var q=document.getElementById('qty');q.value++">+</button>
              </div>
              <div class="cta-wrap">
                <button type="button" id="addToCartBtn" class="btn btn-primary">Add to Cart</button>
                <button type="submit" name="buy_now" class="btn btn-buy">Buy Now</button>
                <a href="products.php" class="btn btn-light">Back to Products</a>
              </div>
              <span id="addCartMsg" style="display:none; color:green; margin-left:10px;">Added to cart!</span>
            </form>
          </div>
        </div>
        <!-- Tabs -->
        <div class="pd-card mt-3">
          <div class="tabs">
            <button class="tab-btn active" data-tab="descTab">Description</button>
            <button class="tab-btn" data-tab="specsTab">Specifications</button>
            <button class="tab-btn" data-tab="shipTab">Shipping & Returns</button>
          </div>
          <div id="descTab" class="tab-pane active"><p class="mb-0"><?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?></p></div>
          <div id="specsTab" class="tab-pane"><table class="specs"><tr><td><?php echo htmlspecialchars($product['specs'] ?? ''); ?></td></tr></table></div>
          <div id="shipTab" class="tab-pane">
            <p class="mb-2"><strong>Delivery</strong></p>
            <p class="text-muted mb-3">Standard (3–5 days): Rs. 200 · Express (1–2 days): Rs. 400</p>
            <p class="mb-2"><strong>Returns</strong></p>
            <p class="text-muted mb-0">7-day return policy for unused items in original packaging.</p>
          </div>
        </div>
      </div>
    </div>
    <?php else: ?>
      <div class="alert alert-warning">Product not found.</div>
    <?php endif; ?>
    <!-- Related -->
    <div class="row mt-4" id="relatedRow"></div>
  </main>
  
  <!-- Footer include -->
  <?php include '../includes/footer.php'; ?>
  <script src="../assests/js/include.js"></script>
  <!-- <script src="../assests/js/productDetail.js"></script> -->

  <!-- Success Modal -->
  <div id="cartModal" style="display:none; position:fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:30px 40px; border-radius:8px; box-shadow:0 2px 12px #0002; text-align:center;">
      <h5 style="margin-bottom:16px;">Product successfully added to the cart!</h5>
      <button onclick="document.getElementById('cartModal').style.display='none'" class="btn btn-primary">OK</button>
    </div>
  </div>
  <script>
document.getElementById('addToCartBtn').onclick = function(e) {
    e.preventDefault();
    var product_id = document.querySelector('input[name="product_id"]').value;
    var quantity = document.getElementById('qty').value;
    var btn = this;
    btn.disabled = true;
    fetch('ajax_add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + encodeURIComponent(product_id) + '&quantity=' + encodeURIComponent(quantity)
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            document.getElementById('cartModal').style.display = 'flex';
        }
    });
};
</script>

</body>
</html>
