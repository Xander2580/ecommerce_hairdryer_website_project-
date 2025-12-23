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
    <title>Frequently Asked Questions - StylePro Essentials</title>
    <link href="assests/css/home.css" rel="stylesheet">
    <link href="assests/css/customerdashboard.css" rel="stylesheet">
    <link href="assests/css/profile.css" rel="stylesheet">
    <style>
        .faq-question { font-weight: 600; margin-top: 18px; }
        .faq-answer { margin-bottom: 16px; color: #444; }
    </style>
</head>
<body>
    <div data-include="includes/navbar.php"></div>
    <div class="support-container" style="max-width:700px;margin:40px auto;background:#fff;border-radius:8px;box-shadow:0 2px 8px #eee;padding:32px;">
        <h2>Frequently Asked Questions (FAQ)</h2>
        <div>
            <div class="faq-question">Q: How do I place an order?</div>
            <div class="faq-answer">A: Browse our products, add your desired items to the cart, and proceed to checkout. Follow the on-screen instructions to complete your purchase.</div>

            <div class="faq-question">Q: What payment methods do you accept?</div>
            <div class="faq-answer">A: We accept major debit/credit cards, digital wallets, and cash on delivery (COD) within Nepal.</div>

            <div class="faq-question">Q: How can I track my order?</div>
            <div class="faq-answer">A: Once your order is shipped, you will receive a tracking number via email or SMS. You can also view your order status in your dashboard.</div>

            <div class="faq-question">Q: How long does delivery take?</div>
            <div class="faq-answer">A: Delivery usually takes 2-5 business days within Nepal. Remote areas may require additional time.</div>

            <div class="faq-question">Q: Can I return or exchange a product?</div>
            <div class="faq-answer">A: Yes, delivered products can be returned within 7 days if they meet our return policy. Please visit the <a href="returns.php">Returns</a> page for details.</div>

            <div class="faq-question">Q: Do your products come with a warranty?</div>
            <div class="faq-answer">A: Yes, all electrical styling tools come with a 1-year limited warranty. See our <a href="warranty.php">Warranty</a> page for more information.</div>

            <div class="faq-question">Q: How do I contact customer support?</div>
            <div class="faq-answer">A: You can reach our support team via the <a href="support.php">Support</a> page. Fill out the form and we’ll get back to you as soon as possible.</div>

            <div class="faq-question">Q: Do you ship internationally?</div>
            <div class="faq-answer">A: Currently, we only ship within Nepal.</div>
        </div>
        <div style="margin-top:24px;">
            <a href="customer/dashboard.php">&larr; Back to Dashboard</a>
        </div>
    </div>
    <div data-include="includes/footer.php"></div>
    <script src="assests/js/include.js"></script>
</body>
</html>