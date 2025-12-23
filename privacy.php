<?php
// privacy.php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - StylePro Essentials</title>
    <link href="assests/css/home.css" rel="stylesheet">
    <link href="assests/css/customerdashboard.css" rel="stylesheet">
    <link href="assests/css/profile.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <section style="background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: #fff; padding: 60px 0 40px 0;">
        <div class="container-fluid">
            <h1 class="display-5 fw-bold mb-2 text-center">Privacy Policy</h1>
            <p class="lead mb-0 text-center" style="max-width:700px;margin:0 auto;">Your privacy is important to us. Learn how we collect, use, and protect your information.</p>
        </div>
    </section>

    <main class="container-fluid py-5" style="background:#fff;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card p-4">
                    <h2 class="mb-3">1. Information We Collect</h2>
                    <p>We collect information you provide when you register, place an order, contact us, or use our services. This may include your name, email, phone, address, and payment details.</p>

                    <h2 class="mb-3">2. How We Use Your Information</h2>
                    <ul>
                        <li>To process orders and provide customer support</li>
                        <li>To improve our products and services</li>
                        <li>To send updates, offers, and important notifications</li>
                        <li>To comply with legal obligations</li>
                    </ul>

                    <h2 class="mb-3">3. Sharing Your Information</h2>
                    <p>We do not sell or rent your personal information. We may share it with trusted partners (such as payment processors or delivery services) only as needed to fulfill your order or provide our services.</p>

                    <h2 class="mb-3">4. Data Security</h2>
                    <p>We use industry-standard security measures to protect your data. However, no method of transmission over the Internet is 100% secure.</p>

                    <h2 class="mb-3">5. Cookies & Tracking</h2>
                    <p>We use cookies to enhance your experience and analyze site usage. You can control cookies through your browser settings.</p>

                    <h2 class="mb-3">6. Your Rights</h2>
                    <ul>
                        <li>You can access, update, or delete your personal information by logging into your account or contacting us.</li>
                        <li>You can opt out of marketing emails at any time.</li>
                    </ul>

                    <h2 class="mb-3">7. Changes to This Policy</h2>
                    <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with the updated date.</p>

                    <h2 class="mb-3">8. Contact Us</h2>
                    <p>If you have any questions about our privacy practices, please contact us at <a href="mailto:support@styleproessentials.com">support@styleproessentials.com</a>.</p>

                    <p class="text-muted mt-4">Last updated: September 14, 2025</p>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assests/js/include.js"></script>
</body>
</html>