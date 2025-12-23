<?php
// admin/edit_products.php - Edit Product Details
require_once '../config/database.php';
requireAdminLogin();

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
    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $base = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($name, PATHINFO_FILENAME));
    return $base . '_' . time() . ($ext ? '.' . $ext : '');
}

// ---------- Validate & Fetch Product ----------
if (!isset($_GET['product_id']) || !ctype_digit($_GET['product_id'])) {
    $_SESSION['msg'] = "No valid product selected.";
    header("Location: productmanagement.php");
    exit();
}
$product_id = (int) $_GET['product_id'];

$stmt = $conn->prepare("SELECT product_id, product_name, description, main_image, price, stock_quantity, status FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    $_SESSION['msg'] = "Product not found.";
    header("Location: productmanagement.php");
    exit();
}
$product = $res->fetch_assoc();

// ---------- Handle Update ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_name   = trim($_POST['product_name'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $price          = (float) ($_POST['price'] ?? 0);
    $stock_quantity = (int) ($_POST['stock_quantity'] ?? 0);
    $status         = $_POST['status'] ?? 'inactive';

    // image handling (optional replacement)
$newImagePath = null;
$oldImagePath = $product['main_image']; // keep old for deletion after successful update

if (!empty($_FILES['main_image']['name'])) {
    $allowedExts  = ['jpg','jpeg','png','gif','webp'];
    $allowedMimes = ['image/jpeg','image/png','image/gif','image/webp'];
    $sizeLimit    = 5 * 1024 * 1024; // 5MB

    $origName = $_FILES['main_image']['name'];
    $tmpPath  = $_FILES['main_image']['tmp_name'];
    $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $mime     = @mime_content_type($tmpPath);
    $size     = (int) $_FILES['main_image']['size'];

    if (in_array($ext, $allowedExts, true) && in_array($mime, $allowedMimes, true) && $size <= $sizeLimit) {
        // Build upload path without realpath() first
        $rootDir     = realpath(__DIR__ . '/..');              // e.g. /var/www/site
        $uploadDirFs = $rootDir . '/assests/images/products/'; // NOTE: uses your "assests" spelling
        if (!is_dir($uploadDirFs)) {
            mkdir($uploadDirFs, 0777, true);
        }

        $finalName  = safe_filename($origName);                // unique name
        $targetFile = $uploadDirFs . $finalName;

        if (move_uploaded_file($tmpPath, $targetFile)) {
            // store web path relative to site root
            $newImagePath = 'assests/images/products/' . $finalName;
        } else {
            $_SESSION['msg'] = "Image upload failed.";
        }
    } else {
        $_SESSION['msg'] = "Invalid image. Allowed: JPG, PNG, GIF, WEBP up to 5MB.";
    }
}

    // --- build and run the UPDATE ---
    if ($newImagePath) {
        $upd = $conn->prepare("UPDATE products
                            SET product_name=?, description=?, main_image=?, price=?, stock_quantity=?, status=?
                            WHERE product_id=?");
        $upd->bind_param("sssdisi", $product_name, $description, $newImagePath, $price, $stock_quantity, $status, $product_id);
    } else {
        $upd = $conn->prepare("UPDATE products
                            SET product_name=?, description=?, price=?, stock_quantity=?, status=?
                            WHERE product_id=?");
        $upd->bind_param("ssdisi", $product_name, $description, $price, $stock_quantity, $status, $product_id);
    }

    if ($upd->execute()) {
        // If we uploaded a new file, delete the old one to avoid "copies"
        if ($newImagePath && !empty($oldImagePath)) {
            $oldFs = realpath($rootDir . '/' . $oldImagePath);
            $uploadsFs = realpath($rootDir . '/assests/images/products');
            if ($oldFs && $uploadsFs && str_starts_with($oldFs, $uploadsFs) && is_file($oldFs)) {
                @unlink($oldFs);
            }
        }
        $_SESSION['msg'] = "Product updated successfully.";
        header("Location: productmanagement.php");
        exit();
    } else {
        $_SESSION['msg'] = "Product update failed.";
        // keep attempted values in the form
        $product['product_name']   = $product_name;
        $product['description']    = $description;
        $product['price']          = $price;
        $product['stock_quantity'] = $stock_quantity;
        $product['status']         = $status;
        if ($newImagePath) $product['main_image'] = $newImagePath;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/admindashboard.css" rel="stylesheet">
    <style>
        .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
        .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
        .form-group input, .form-group textarea, .form-group select {
            width:100%; padding:10px 12px; border:1px solid #ced4da; border-radius:8px; background:#fbfcfe;
        }
        .thumb { height:80px; width:auto; border-radius:8px; border:1px solid #e9ecef; background:#fff; }
        @media (max-width: 900px) { .form-grid-2, .form-grid-3 { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="admin-main-content">
        <?php if ($m = flash('msg')): ?>
            <div class="alert alert-info" style="border-radius:12px;"><?php echo htmlspecialchars($m); ?></div>
        <?php endif; ?>

        <div class="dashboard-header">
            <div>
                <h1>Edit Product</h1>
                <p>Update the product details below</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Update Product Details</h6>
            </div>
            <div class="card-body">
                <form action="edit_products.php?product_id=<?php echo (int)$product['product_id']; ?>" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="product_name">Product Name</label>
                            <input type="text" id="product_name" name="product_name"
                                   value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3" required><?php
                                echo htmlspecialchars($product['description']); ?></textarea>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" step="0.01" id="price" name="price"
                                   value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="stock_quantity">Stock Quantity</label>
                            <input type="number" id="stock_quantity" name="stock_quantity"
                                   value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="active"  <?php if ($product['status']==='active')  echo 'selected'; ?>>Active</option>
                                <option value="inactive"<?php if ($product['status']==='inactive')echo 'selected'; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Current Image</label>
                            <?php
                            $img = $product['main_image'];
                            $img_path = '';
                            if (!empty($img)) {
                                // If the path already starts with 'assests/images/products/' or 'uploads/products/', just prepend '../'
                                if (strpos($img, 'assests/images/products/') === 0 || strpos($img, 'uploads/products/') === 0) {
                                    $img_path = '../' . $img;
                                } elseif (strpos($img, '/') === false) {
                                    // Only filename, assume assests/images/products/
                                    $img_path = '../assests/images/products/' . $img;
                                } else {
                                    // Some other relative path
                                    $img_path = '../' . $img;
                                }
                            }
                            ?>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <?php if (!empty($img_path) && file_exists($img_path)): ?>
                                    <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Current Image" class="thumb">
                                    <small class="text-muted"><?php echo htmlspecialchars($img); ?></small>
                                <?php elseif (!empty($img_path)): ?>
                                    <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Current Image" class="thumb">
                                    <small class="text-danger">Image not found</small>
                                <?php else: ?>
                                    <span class="text-muted">No image uploaded</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="main_image">Replace Image (optional)</label>
                            <input type="file" id="main_image" name="main_image" accept="image/*">
                            <small class="text-muted" style="display:block;margin-top:6px;">Allowed: JPG, PNG, GIF, WEBP (max 5MB)</small>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:space-between; margin-top:20px;">
                        <button type="submit" name="edit_product" class="btn btn-primary">Update Product</button>
                        <a href="productmanagement.php" class="btn btn-secondary">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.admin-main-content -->
</div>
</body>
</html>
