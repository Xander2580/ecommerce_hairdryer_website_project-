<?php
// about.php
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - StylePro Essentials</title>
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/customerdashboard.css" rel="stylesheet">
    <link href="../assests/css/profile.css" rel="stylesheet">
    <style>
        .about-team-img {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
        }
        .about-team-img img {
            width: 70%;
            max-width: 420px;
            border-radius: 12px;
            box-shadow: 0 2px 12px #001bb71a;
        }
        @media (max-width: 991px) {
            .about-team-img img { width: 100%; }
        }
        .timeline {
            border-left: 3px solid #0046ff;
            margin: 2rem 0 2rem 1rem;
            padding-left: 2rem;
        }
        .timeline-event {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .timeline-event:before {
            content: '';
            position: absolute;
            left: -2.2rem;
            top: 0.3rem;
            width: 1rem;
            height: 1rem;
            background: #0046ff;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #0046ff;
        }
        .testimonial-card {
            background: #f5f8ff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px #001bb71a;
        }
        .testimonial-card .fa-quote-left {
            color: #0046ff;
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .testimonial-author {
            font-weight: 600;
            color: #001bb7;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="about-hero" style="background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: #fff; padding: 60px 0 40px 0;">
        <div class="container-fluid">
            <h1 class="display-5 fw-bold mb-2 text-center">About StylePro Essentials</h1>
            <p class="lead mb-0 text-center" style="max-width:700px;margin:0 auto;">Empowering salons and individuals with professional hair styling tools and care solutions.</p>
        </div>
    </section>

    <section class="about-content" style="background:#fff;">
        <div class="container-fluid py-5">
            <div class="about-team-img">
                <img src="../assests/images/products/about_team.jpg" alt="Our Team">
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="mb-3">Who We Are</h2>
                    <p>
                        <strong>StylePro Essentials</strong> was founded with a passion for delivering high-quality, innovative hair styling tools and products to both professionals and home users. Our mission is to make salon-quality results accessible to everyone, everywhere.
                    </p>
                    <ul class="about-list" style="list-style: none; padding-left: 0;">
                        <li style="margin-bottom:10px;"><i class="fas fa-check-circle text-primary"></i> Trusted by top salons and stylists</li>
                        <li style="margin-bottom:10px;"><i class="fas fa-check-circle text-primary"></i> Wide range of professional-grade products</li>
                        <li style="margin-bottom:10px;"><i class="fas fa-check-circle text-primary"></i> Dedicated to customer satisfaction and support</li>
                        <li style="margin-bottom:10px;"><i class="fas fa-check-circle text-primary"></i> Fast shipping and easy returns</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <h2 class="mb-3">Our Journey</h2>
                    <div class="timeline">
                        <div class="timeline-event">
                            <strong>2020</strong> – StylePro Essentials is founded with a vision to revolutionize hair care in Nepal.
                        </div>
                        <div class="timeline-event">
                            <strong>2021</strong> – Launched our first line of professional hair dryers and straighteners.
                        </div>
                        <div class="timeline-event">
                            <strong>2022</strong> – Partnered with leading salons and expanded our product range.
                        </div>
                        <div class="timeline-event">
                            <strong>2023</strong> – Served over 10,000 happy customers and introduced online shopping.
                        </div>
                        <div class="timeline-event">
                            <strong>2025</strong> – Recognized as a top brand for quality and customer service in Nepal.
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-4 mb-4">
                    <div class="about-feature-card" style="background:#f5f8ff;border-radius:8px;padding:24px;box-shadow:0 2px 8px #001bb71a;">
                        <i class="fas fa-bolt fa-2x text-primary mb-3"></i>
                        <h5>Our Mission</h5>
                        <p>To empower everyone to achieve their best look with safe, reliable, and innovative hair styling solutions.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="about-feature-card" style="background:#f5f8ff;border-radius:8px;padding:24px;box-shadow:0 2px 8px #001bb71a;">
                        <i class="fas fa-heart fa-2x text-primary mb-3"></i>
                        <h5>Our Values</h5>
                        <p>Quality, integrity, and customer care are at the heart of everything we do. We believe in building lasting relationships with our customers.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="about-feature-card" style="background:#f5f8ff;border-radius:8px;padding:24px;box-shadow:0 2px 8px #001bb71a;">
                        <i class="fas fa-users fa-2x text-primary mb-3"></i>
                        <h5>Our Team</h5>
                        <p>Our diverse team of experts and stylists are dedicated to bringing you the latest trends and best products in hair care.</p>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h3 class="mb-3">Why Choose StylePro Essentials?</h3>
                    <p>
                        We combine advanced technology, premium materials, and a passion for beauty to deliver products that truly make a difference. Whether you’re a professional stylist or styling at home, you can trust StylePro Essentials for outstanding results.
                    </p>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-8 mx-auto text-center">
                    <h3 class="mb-4">What Our Customers Say</h3>
                </div>
                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left"></i>
                        <span>StylePro Essentials has transformed my salon! The tools are reliable and my clients love the results.</span>
                        <div class="testimonial-author">— S. Shrestha, Salon Owner</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left"></i>
                        <span>Fast delivery and amazing customer support. I recommend StylePro to all my friends!</span>
                        <div class="testimonial-author">— R. Karki, Customer</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left"></i>
                        <span>The hair dryer I bought is the best I’ve ever used. Worth every rupee!</span>
                        <div class="testimonial-author">— P. Lama, Home User</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-cta" style="background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%); color: #fff; padding: 40px 0;">
        <div class="container-fluid text-center">
            <h2 class="mb-3">Ready to Experience Professional Hair Care?</h2>
            <a href="products.php" class="btn btn-light btn-lg">Browse Our Products</a>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
    <script src="../assests/js/include.js"></script>
</body>
</html>