<?php
require_once '../config/database.php';
$conn = getDatabaseConnection();

// --- Handle filters, sorting, and pagination ---
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// --- Build SQL ---
$where = ["status = 1"];
if ($category_id) $where[] = "category_id = $category_id";
if ($min_price) $where[] = "price >= $min_price";
if ($max_price) $where[] = "price <= $max_price";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$order_sql = "ORDER BY product_id DESC";
if ($sort === 'price_asc') $order_sql = "ORDER BY price ASC";
if ($sort === 'price_desc') $order_sql = "ORDER BY price DESC";
if ($sort === 'newest') $order_sql = "ORDER BY created_by DESC";

$total_sql = "SELECT COUNT(*) FROM products $where_sql";
$total_result = $conn->query($total_sql);
$total_products = $total_result->fetch_row()[0];

$product_sql = "SELECT * FROM products $where_sql $order_sql LIMIT $offset, $per_page";
$product_result = $conn->query($product_sql);

// For category filter UI
$cat_result = $conn->query("SELECT category_id, category_name FROM categories");
$categories = [];
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - StylePro Essentials</title>
    <link href="../assests/css/home.css" rel="stylesheet" />
    <link href="../assests/css/customerdashboard.css" rel="stylesheet" />
    <link href="../assests/css/profile.css" rel="stylesheet" />
</head>
<body data-page="products">
    <!-- Navigation Include -->
    <div data-include="../includes/navbar.php"></div>
    
    <!-- Rest of your content... -->
    <main>
        <!-- Header -->
        <div class="dashboard-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <h1 class="display-6 fw-bold">Products</h1>
                        <p class="lead mb-0">Shop professional hair dryers, straighteners, and styling tools</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid my-4">
            <div class="row">
                <!-- Sidebar (static UI only) -->
                <aside class="col-lg-3 mb-4">
                    <form method="get" class="card">
                        <div class="card-header"><h5 class="mb-0">Filters</h5></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="mb-2">Category</h6>
                                <ul class="list-unstyled mb-0">
                                    <li>
                                        <label>
                                            <input type="radio" name="category_id" value="0" <?= $category_id == 0 ? 'checked' : '' ?>> All
                                        </label>
                                    </li>
                                    <?php foreach ($categories as $cat): ?>
                                    <li>
                                        <label>
                                            <input type="radio" name="category_id" value="<?= $cat['category_id'] ?>" <?= $category_id == $cat['category_id'] ? 'checked' : '' ?>>
                                            <?= htmlspecialchars($cat['category_name']) ?>
                                        </label>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <h6 class="mb-2">Price Range</h6>
                                <div class="d-flex" style="gap:8px;">
                                    <input type="number" class="form-control" name="min_price" placeholder="Min" value="<?= $min_price ?: '' ?>">
                                    <input type="number" class="form-control" name="max_price" placeholder="Max" value="<?= $max_price ?: '' ?>">
                                </div>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Apply</button>
                        </div>
                    </form>
                </aside>

                <!-- Products grid -->
                <section class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small">Showing <?= $product_result->num_rows ?> of <?= $total_products ?> items</div>
                        <form method="get" class="d-inline">
                            <input type="hidden" name="category_id" value="<?= $category_id ?>">
                            <input type="hidden" name="min_price" value="<?= $min_price ?>">
                            <input type="hidden" name="max_price" value="<?= $max_price ?>">
                            <label for="sort" class="me-2">Sort by:</label>
                            <select id="sort" name="sort" class="form-control" style="width:auto; display:inline-block;" onchange="this.form.submit()">
                                <option value="">Most Popular</option>
                                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                            </select>
                        </form>
                    </div>

                    <div class="row">
                        <?php if ($product_result->num_rows): while ($product = $product_result->fetch_assoc()): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <img src="../assests/images/products/<?= htmlspecialchars($product['main_image']) ?>"
                                     class="img-fluid"
                                     alt="<?= htmlspecialchars($product['product_name']) ?>"
                                     loading="lazy">
                                <div class="card-body">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($product['product_name']) ?></h6>
                                    <p class="mb-2">
                                        <small class="text-success fw-bold">
                                            <?= formatPrice($product['price']) ?>
                                            <?php if ($product['discount_price'] && $product['discount_price'] < $product['price']): ?>
                                                <span class="text-danger text-decoration-line-through ms-2"><?= formatPrice($product['discount_price']) ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </p>
                                    <a href="product_detail.php?id=<?= $product['product_id'] ?>" class="btn btn-light btn-sm">View</a>
                                    <button type="button" class="btn btn-primary btn-sm add-to-cart-btn" data-id="<?= $product['product_id'] ?>">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; else: ?>
                        <div class="col-12"><p>No products found.</p></div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php
                    $total_pages = ceil($total_products / $per_page);
                    if ($total_pages > 1):
                    ?>
                    <nav class="mt-3">
                        <ul class="pagination">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Prev</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>
    
    <!-- Footer Include -->
    <div data-include="../includes/footer.php"></div>
    <script src="../assests/js/include.js"></script>

    <!-- Add this modal for the dialog box -->
    <div id="cartModal" style="display:none; position:fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:9999; align-items:center; justify-content:center;">
      <div style="background:#fff; padding:30px 40px; border-radius:8px; box-shadow:0 2px 12px #0002; text-align:center;">
        <h5 style="margin-bottom:16px;">Product successfully added to the cart!</h5>
        <button onclick="document.getElementById('cartModal').style.display='none'" class="btn btn-primary">OK</button>
      </div>
    </div>

    <script>
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
          }
        });
      });
    });
    </script>
</body>
</html>
