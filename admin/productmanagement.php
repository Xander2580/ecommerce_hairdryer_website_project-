<?php
// admin/productmanagement.php - Manage Products
require_once '../config/database.php';
requireAdminLogin();

$user = getCurrentUser();
$conn = getDatabaseConnection();

// ---------- Helpers ----------
function flash($key) {
    if (!empty($_SESSION[$key])) {
        $m = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $m;
    }
    return '';
}
function safe_filename($name) {
    // keep extension, sanitize basename
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $base = pathinfo($name, PATHINFO_FILENAME);
    $base = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
    return $base . '_' . time() . ($ext ? '.' . strtolower($ext) : '');
}

// ---------- DELETE PRODUCT ----------
if (isset($_GET['delete_id'])) {
    $product_id = (int) $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $_SESSION['msg'] = $stmt->execute() ? "Product deleted successfully." : "Product deletion failed.";
    header("Location: productmanagement.php");
    exit();
}

// ---------- ADD / EDIT PRODUCT ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name   = trim($_POST['product_name'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $price          = (float) ($_POST['price'] ?? 0);
    $stock_quantity = (int) ($_POST['stock_quantity'] ?? 0);
    $status         = $_POST['status'] ?? 'inactive';

    // Handle image upload (to main_image)
    $imagePath = null;
    if (!empty($_FILES['main_image']['name'])) {
        $allowedExts  = ['jpg','jpeg','png','gif','webp'];
        $allowedMimes = ['image/jpeg','image/png','image/gif','image/webp'];
        $sizeLimit    = 5 * 1024 * 1024; // 5MB

        $origName  = $_FILES['main_image']['name'];
        $tmpPath   = $_FILES['main_image']['tmp_name'];
        $mimeType  = mime_content_type($tmpPath);
        $ext       = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExts, true) && in_array($mimeType, $allowedMimes, true) && $_FILES['main_image']['size'] <= $sizeLimit) {
            $uploadDirFs = realpath(__DIR__ . '/..') . '/uploads/products/';
            if (!is_dir($uploadDirFs)) {
                @mkdir($uploadDirFs, 0777, true);
            }
            $finalName  = safe_filename($origName);
            $targetFile = $uploadDirFs . $finalName;

            if (move_uploaded_file($tmpPath, $targetFile)) {
                // path relative to site root from /admin/*
                $imagePath = 'uploads/products/' . $finalName;
            } else {
                $_SESSION['msg'] = "Image upload failed.";
            }
        } else {
            $_SESSION['msg'] = "Invalid image file. Allowed: JPG, PNG, GIF, WEBP up to 5MB.";
        }
    }

    // ADD
    if (isset($_POST['add_product'])) {
        $stmt = $conn->prepare(
            "INSERT INTO products (product_name, description, main_image, price, stock_quantity, status)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->bind_param("sssdis", $product_name, $description, $imagePath, $price, $stock_quantity, $status);
        $_SESSION['msg'] = $stmt->execute() ? "Product added successfully." : "Product addition failed.";
        header("Location: productmanagement.php");
        exit();
    }

    // EDIT
    if (isset($_POST['edit_product'])) {
        $product_id = (int) $_POST['product_id'];

        if ($imagePath) {
            $stmt = $conn->prepare(
                "UPDATE products
                 SET product_name = ?, description = ?, main_image = ?, price = ?, stock_quantity = ?, status = ?
                 WHERE product_id = ?"
            );
            $stmt->bind_param("sssdisi", $product_name, $description, $imagePath, $price, $stock_quantity, $status, $product_id);
        } else {
            // keep old image unchanged
            $stmt = $conn->prepare(
                "UPDATE products
                 SET product_name = ?, description = ?, price = ?, stock_quantity = ?, status = ?
                 WHERE product_id = ?"
            );
            $stmt->bind_param("ssdisi", $product_name, $description, $price, $stock_quantity, $status, $product_id);
        }
        $_SESSION['msg'] = $stmt->execute() ? "Product updated successfully." : "Product update failed.";
        header("Location: productmanagement.php");
        exit();
    }
}

// ---------- FETCH PRODUCTS ----------
$products_result = $conn->query("SELECT * FROM products ORDER BY product_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Hair Care Store</title>
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/admindashboard.css" rel="stylesheet">
    <style>
        .form-grid-2 { display:grid; grid-template-columns: 1fr 1fr; gap:20px; }
        .form-grid-3 { display:grid; grid-template-columns: 1fr 1fr 1fr; gap:20px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
        .form-group input, .form-group textarea, .form-group select {
            width:100%; padding:10px 12px; border:1px solid #e4e8ee; border-radius:10px; background:#fbfcfe;
        }
        @media (max-width:1024px){ .form-grid-2,.form-grid-3{ grid-template-columns:1fr; } }
        .thumb { height:60px; width:auto; border-radius:8px; background:#fff; border:1px solid #e9ecef; }
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
            <li><a class="nav-link active" href="productmanagement.php">Product Management</a></li>
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
        <?php if ($m = flash('msg')): ?>
            <div class="alert alert-info" style="border-radius:12px;"><?php echo htmlspecialchars($m); ?></div>
        <?php endif; ?>

        <div class="dashboard-header">
            <div>
                <h1>Product Management</h1>
                <p>Manage your product inventory</p>
            </div>
        </div>

        <!-- Add Product Form -->
        <div class="card">
            <div class="card-header"><h6>Add New Product</h6></div>
            <div class="card-body">
                <form action="productmanagement.php" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="product_name">Product Name</label>
                            <input type="text" id="product_name" name="product_name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="2" required></textarea>
                        </div>
                    </div>

                    <div class="form-grid-3" style="margin-top:20px;">
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" step="0.01" id="price" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="stock_quantity">Stock Quantity</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:20px;">
                        <label for="main_image">Product Image</label>
                        <input type="file" id="main_image" name="main_image" accept="image/*">
                        <small class="text-muted" style="display:block;margin-top:6px;">Allowed: JPG, PNG, GIF, WEBP (max 5MB)</small>
                    </div>

                    <button type="submit" name="add_product" class="btn btn-primary" style="margin-top:16px;">Add Product</button>
                </form>
            </div>
        </div>

        <!-- Product Table -->
        <div class="card" style="margin-top:22px;">
            <div class="card-header"><h6>All Products</h6></div>
            <div class="card-body">
                <?php if ($products_result && $products_result->num_rows > 0): ?>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $products_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                <?php
                                $img = $row['main_image'];
                                if (strpos($img, '/') === false) {
                                    // Old image, just filename, assume assests/images/products/
                                    $img_path = '../assests/images/products/' . $img;
                                } else {
                                    // New image, full relative path
                                    $img_path = '../' . $img;
                                }
                                ?>
                                <?php if (!empty($img)): ?>
                                    <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Product Image" class="thumb">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><strong><?php echo number_format((float)$row['price'], 2); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo ($row['stock_quantity'] > 0) ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo (int)$row['stock_quantity']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo ($row['status'] === 'active') ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_products.php?product_id=<?php echo (int)$row['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?delete_id=<?php echo (int)$row['product_id']; ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-4">
                        <h5 class="text-muted">No products found</h5>
                        <p class="text-muted">Add your first product using the form above.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- /.admin-main-content -->
</div><!-- /.container-fluid -->
</body>
</html>
