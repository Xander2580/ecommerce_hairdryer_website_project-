<?php
// cookie.php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie Policy - StylePro Essentials</title>
    <link href="assests/css/home.css" rel="stylesheet">
    <link href="assests/css/customerdashboard.css" rel="stylesheet">
    <link href="assests/css/profile.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <section style="background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: #fff; padding: 60px 0 40px 0;">
        <div class="container-fluid">
            <h1 class="display-5 fw-bold mb-2 text-center">Cookie Policy</h1>
            <p class="lead mb-0 text-center" style="max-width:700px;margin:0 auto;">How we use cookies and similar technologies on StylePro Essentials.</p>
        </div>
    </section>

    <main class="container-fluid py-5" style="background:#fff;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card p-4">
                    <h2 class="mb-3">1. What Are Cookies?</h2>
                    <p>Cookies are small text files stored on your device when you visit a website. They help us remember your preferences, improve your experience, and analyze site usage.</p>

                    <h2 class="mb-3">2. How We Use Cookies</h2>
                    <ul>
                        <li>To keep you logged in and maintain your session</li>
                        <li>To remember your preferences (like language or cart contents)</li>
                        <li>To analyze website traffic and usage patterns</li>
                        <li>To provide relevant offers and content</li>
                    </ul>

                    <h2 class="mb-3">3. Types of Cookies We Use</h2>
                    <ul>
                        <li><strong>Essential Cookies:</strong> Required for the website to function (e.g., login, cart).</li>
                        <li><strong>Performance Cookies:</strong> Help us understand how visitors use our site.</li>
                        <li><strong>Functionality Cookies:</strong> Remember your preferences and settings.</li>
                        <li><strong>Advertising Cookies:</strong> (If used) Deliver relevant ads and measure their effectiveness.</li>
                    </ul>

                    <h2 class="mb-3">4. Managing Cookies</h2>
                    <p>You can control and delete cookies through your browser settings. However, disabling cookies may affect your experience on our site.</p>

                    <h2 class="mb-3">5. Third-Party Cookies</h2>
                    <p>We may use third-party services (like analytics or payment providers) that set their own cookies. We do not control these cookies.</p>

                    <h2 class="mb-3">6. Changes to This Policy</h2>
                    <p>We may update this Cookie Policy from time to time. Changes will be posted on this page with the updated date.</p>

                    <h2 class="mb-3">7. Contact Us</h2>
                    <p>If you have any questions about our use of cookies, please contact us at <a href="mailto:support@styleproessentials.com">support@styleproessentials.com</a>.</p>

                    <p class="text-muted mt-4">Last updated: September 14, 2025</p>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assests/js/include.js"></script>
</body>
</html>