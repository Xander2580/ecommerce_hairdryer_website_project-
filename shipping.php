<?php
require_once 'config/database.php';
requireCustomerLogin();

$customer = getCurrentCustomer();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Information - StylePro Essentials</title>
    <link href="assests/css/home.css" rel="stylesheet">
    <link href="assests/css/customerdashboard.css" rel="stylesheet">
    <link href="assests/css/profile.css" rel="stylesheet">
</head>
<body>
    <div data-include="includes/navbar.php"></div>
    <div class="support-container" style="max-width:700px;margin:40px auto;background:#fff;border-radius:8px;box-shadow:0 2px 8px #eee;padding:32px;">
        <h2>Shipping Information</h2>
        <p>
            At <strong>StylePro Essentials</strong>, we strive to deliver your orders quickly and safely. Please review our shipping policy below:
        </p>
        <ul style="margin-bottom:20px;">
            <li><strong>Processing Time:</strong> Orders are processed within 1-2 business days after payment confirmation.</li>
            <li><strong>Shipping Time:</strong> Delivery typically takes 2-5 business days within Nepal. Remote areas may require additional time.</li>
            <li><strong>Shipping Charges:</strong> Free shipping on orders over NPR 2,000. For orders below this amount, a flat shipping fee of NPR 150 applies.</li>
            <li><strong>Order Tracking:</strong> Once your order is shipped, you will receive a tracking number via email or SMS.</li>
            <li><strong>Delivery Partners:</strong> We use trusted courier services to ensure safe and timely delivery.</li>
            <li><strong>Address Accuracy:</strong> Please ensure your shipping address and contact number are correct to avoid delays.</li>
            <li><strong>International Shipping:</strong> Currently, we only ship within Nepal.</li>
        </ul>
        <h4>Need Help?</h4>
        <p>
            If you have questions about your shipment or need to update your delivery details, please <a href="support.php">contact our support team</a>.
        </p>
        <div style="margin-top:24px;">
            <a href="customer/dashboard.php">&larr; Back to Dashboard</a>
        </div>
    </div>
    <div data-include="includes/footer.php"></div>
    <script src="assests/js/include.js"></script>
</body>
</html>