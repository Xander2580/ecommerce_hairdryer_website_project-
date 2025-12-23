<?php
require_once 'config/database.php';
requireCustomerLogin();

$customer = getCurrentCustomer();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = trim($_POST['order_id'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    if (empty($order_id) || empty($reason)) {
        $error = "Please fill in all fields.";
    } else {
        $conn = getDatabaseConnection();
        $customer_id = $_SESSION['customer_id'];
        $order_id_esc = mysqli_real_escape_string($conn, $order_id);
        $reason_esc = mysqli_real_escape_string($conn, $reason);

        // Check if order belongs to this customer and is eligible for return
        $sql = "SELECT * FROM orders WHERE order_id = '$order_id_esc' AND customer_id = $customer_id AND order_status = 'delivered'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) === 0) {
            $error = "Invalid order ID or the order is not eligible for return.";
        } else {
            // Insert return request
            $sql = "INSERT INTO return_requests (order_id, customer_id, reason, status, created_at)
                    VALUES ('$order_id_esc', $customer_id, '$reason_esc', 'pending', NOW())";
            if (mysqli_query($conn, $sql)) {
                $success = "Your return request has been submitted. Our team will contact you soon.";
            } else {
                $error = "Failed to submit your return request. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Request - StylePro Essentials</title>
    <link href="assests/css/home.css" rel="stylesheet">
    <link href="assests/css/customerdashboard.css" rel="stylesheet">
    <link href="assests/css/profile.css" rel="stylesheet">
</head>
<body>
    <div data-include="includes/navbar.php"></div>
    <div class="support-container" style="max-width:600px;margin:40px auto;background:#fff;border-radius:8px;box-shadow:0 2px 8px #eee;padding:32px;">
        <h2>Request a Return</h2>
        <p>If you need to return a product, please fill out the form below. Only delivered orders are eligible for return.</p>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="order_id">Order ID *</label>
                <input type="text" id="order_id" name="order_id" class="form-control" required value="<?php echo htmlspecialchars($_POST['order_id'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="reason">Reason for Return *</label>
                <textarea id="reason" name="reason" class="form-control" rows="4" required><?php echo htmlspecialchars($_POST['reason'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn-support">Submit Return Request</button>
        </form>
        <div style="margin-top:24px;">
            <a href="customer/dashboard.php">&larr; Back to Dashboard</a>
        </div>
    </div>
    <div data-include="includes/footer.php"></div>
    <script src="assests/js/include.js"></script>
</body>
</html>