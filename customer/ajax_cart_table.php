<?php
require_once '../config/database.php';
$conn = getDatabaseConnection();

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

ob_start();
?>
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
<?php
$html = ob_get_clean();
echo json_encode([
    'html' => $html,
    'cart_total' => formatPrice($cart_total),
]);