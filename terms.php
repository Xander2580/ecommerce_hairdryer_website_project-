<?php
// terms.php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - StylePro Essentials</title>
    <link href="assests/css/home.css" rel="stylesheet">
    <link href="assests/css/customerdashboard.css" rel="stylesheet">
    <link href="assests/css/profile.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <section style="background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: #fff; padding: 60px 0 40px 0;">
        <div class="container-fluid">
            <h1 class="display-5 fw-bold mb-2 text-center">Terms of Service</h1>
            <p class="lead mb-0 text-center" style="max-width:700px;margin:0 auto;">Please read these terms and conditions carefully before using our website or services.</p>
        </div>
    </section>

    <main class="container-fluid py-5" style="background:#fff;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card p-4">
                    <h2 class="mb-3">1. Acceptance of Terms</h2>
                    <p>By accessing or using the StylePro Essentials website, you agree to be bound by these Terms of Service and all applicable laws and regulations.</p>

                    <h2 class="mb-3">2. Use of the Website</h2>
                    <ul>
                        <li>You must be at least 18 years old or have parental consent to use our services.</li>
                        <li>You agree not to misuse the website or interfere with its normal operation.</li>
                        <li>All content is for personal, non-commercial use unless otherwise stated.</li>
                    </ul>

                    <h2 class="mb-3">3. Orders & Payment</h2>
                    <ul>
                        <li>All orders are subject to acceptance and availability.</li>
                        <li>Prices and product information are subject to change without notice.</li>
                        <li>We reserve the right to refuse or cancel any order at our discretion.</li>
                    </ul>

                    <h2 class="mb-3">4. Returns & Refunds</h2>
                    <p>Returns are accepted within 7 days of delivery for unused products in original packaging. Please see our <a href="returns.php">Returns Policy</a> for details.</p>

                    <h2 class="mb-3">5. Intellectual Property</h2>
                    <p>All content, trademarks, and logos on this site are the property of StylePro Essentials or its licensors. You may not use or reproduce them without permission.</p>

                    <h2 class="mb-3">6. Limitation of Liability</h2>
                    <p>We are not liable for any indirect, incidental, or consequential damages arising from your use of our website or products.</p>

                    <h2 class="mb-3">7. Changes to Terms</h2>
                    <p>We may update these Terms of Service at any time. Changes will be posted on this page with the updated date.</p>

                    <h2 class="mb-3">8. Governing Law</h2>
                    <p>These terms are governed by the laws of Nepal. Any disputes will be subject to the exclusive jurisdiction of the courts of Kathmandu, Nepal.</p>

                    <h2 class="mb-3">9. Contact Us</h2>
                    <p>If you have any questions about these terms, please contact us at <a href="mailto:support@styleproessentials.com">support@styleproessentials.com</a>.</p>

                    <p class="text-muted mt-4">Last updated: September 14, 2025</p>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="assests/js/include.js"></script>
</body>
</html>