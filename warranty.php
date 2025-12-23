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
    <title>Warranty Information - StylePro Essentials</title>
    <link href="assests/css/home.css" rel="stylesheet">
    <link href="assests/css/customerdashboard.css" rel="stylesheet">
    <link href="assests/css/profile.css" rel="stylesheet">
</head>
<body>
    <div data-include="includes/navbar.php"></div>
    <div class="support-container" style="max-width:700px;margin:40px auto;background:#fff;border-radius:8px;box-shadow:0 2px 8px #eee;padding:32px;">
        <h2>Warranty Information</h2>
        <p>
            At <strong>StylePro Essentials</strong>, we stand behind the quality of our professional hair styling tools. Please review our warranty policy below:
        </p>
        <ul style="margin-bottom:20px;">
            <li><strong>Warranty Period:</strong> All electrical hair styling tools come with a 1-year limited warranty from the date of purchase.</li>
            <li><strong>What’s Covered:</strong> The warranty covers manufacturing defects and malfunctions under normal use.</li>
            <li><strong>What’s Not Covered:</strong> Damage due to misuse, accidents, unauthorized repairs, or normal wear and tear is not covered.</li>
            <li><strong>Claim Process:</strong> To claim warranty service, please provide your order ID, purchase date, and a description of the issue via our <a href="support.php">support page</a>.</li>
            <li><strong>Service:</strong> If your product is eligible, we will repair or replace it at no additional cost.</li>
            <li><strong>Proof of Purchase:</strong> Please retain your invoice or order confirmation email as proof of purchase.</li>
        </ul>
        <h4>Need Assistance?</h4>
        <p>
            For warranty claims or questions, please <a href="support.php">contact our support team</a>. We are here to help!
        </p>
        <div style="margin-top:24px;">
            <a href="customer/dashboard.php">&larr; Back to Dashboard</a>
        </div>
    </div>
    <div data-include="includes/footer.php"></div>
    <script src="assests/js/include.js"></script>
</body>
</html>