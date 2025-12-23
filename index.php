<?php
// index.php
$page_title = "StylePro Essentials - Professional Hair Styling Tools";
require_once 'config/database.php';
$conn = getDatabaseConnection();

// Fetch featured products (e.g., latest 3)
$products = [];
$sql = "SELECT product_id, product_name, price, main_image FROM products ORDER BY created_at DESC LIMIT 3";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) $products[] = $row;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>StylePro Essentials - Professional Hair Styling Tools</title>
<link href="assests/css/home.css" rel="stylesheet">
</head>
<body>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="assests/css/home.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    
<!-- Navigation -->
<nav class="navbar">
        <div class="nav-container">
            <a class="navbar-brand" href="index.php">
                StylePro Essentials
            </a>
            <button class="navbar-toggle" type="button" id="navbarToggle">
                <span class="navbar-toggle-icon"></span>
            </button>
            <div class="navbar-menu" id="navbarMenu">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            Home
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item">
                        <a class="nav-link" href="customer/login.php">
                            Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="customer/register.php">
                            Register
                        </a>
                    </li>
                    <li class="nav-item">
                        
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="hero-info">
                        <h1 class="hero-title">StylePro Essentials</h1>
                        <p class="hero-subtitle">Professional Hair Styling Tools for Perfect Results</p>
                        <p class="hero-description">
                            Transform your hair styling experience with our premium quality hair dryers and straighteners. 
                            Get salon-quality results at home with our professional-grade tools.
                        </p>
                        <div class="hero-buttons">
                            <a href="customer/register.php" class="btn btn-primary btn-lg">
                               Get Started
                            </a>
                            <a href="customer/login.php" class="btn btn-outline-light btn-lg">
                                Login
                            </a>
                        </div>
                        <div class="hero-features">
                            <div class="feature-grid">
                                <div class="feature-item">
                                    <span>Premium Quality Products</span>
                                </div>
                                <div class="feature-item">
                                    <span>Fast Delivery</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-image-container">
                        <div class="hero-icon-wrapper">
                           
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Background Elements -->
        <div class="hero-bg-elements">
            <div class="bg-element element-1"></div>
            <div class="bg-element element-2"></div>
            <div class="bg-element element-3"></div>
        </div>
    </section>
    <!-- Product Images Section -->
<section class="product-images-section">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">Featured Products</h2>
            <p class="section-subtitle">Explore our top hair styling tools</p>
        </div>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-item">
                <img src="assests/images/products/<?= htmlspecialchars($product['main_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image">
                <p class="product-name"><?= htmlspecialchars($product['product_name']) ?></p>
                <p class="product-price">Rs. <?= number_format($product['price']) ?></p>
                <a href="customer/product_detail.php?id=<?= $product['product_id'] ?>" class="btn btn-primary btn-sm">View Details</a>
            </div>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
            <div class="product-item">
                <p>No featured products found.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

 <!-- Footer -->
<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-brand">
                    <h5 class="footer-title">
                        StylePro Essentials
                    </h5>
                    <p class="footer-description">
                        Your trusted partner for professional hair styling tools. We provide premium quality 
                        hair dryers and straighteners to help you achieve salon-quality results at home.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-link" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#0046ff;color:#fff;margin-right:8px;font-size:1.2rem;">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#1da1f2;color:#fff;margin-right:8px;font-size:1.2rem;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#e1306c;color:#fff;margin-right:8px;font-size:1.2rem;">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#0a66c2;color:#fff;font-size:1.2rem;">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    </div>
                </div>
            </div>
            <div class="footer-section">
                <h5 class="footer-title">Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="customer/login.php">Customer Login</a></li>
                    <li><a href="customer/register.php">Register</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h5 class="footer-title">Contact Info</h5>
                <div class="footer-contact">
                    <div class="contact-item">
                        <span>Kathmandu, Nepal</span>
                    </div>
                    <div class="contact-item">
                        <span>+977 9762261869</span>
                    </div>
                    <div class="contact-item">
                        <span>info@styleproessentials.com</span>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="footer-divider">
        
        <div class="footer-bottom">
            <div class="footer-copyright">
                <p class="copyright">
                    &copy; 2025 StylePro Essentials. All rights reserved.
                </p>
            </div>
            <div class="footer-credits">
                <p class="developer-credits">
                    <small>Developed by: Rojina, Prinsha, Shrutina, Pamas & Kushal</small>
                </p>
            </div>
        </div>
    </div>
</footer>

    
    <!-- Custom JavaScript -->
    <script>
        // Mobile navbar toggle
        document.getElementById('navbarToggle').addEventListener('click', function() {
            document.getElementById('navbarMenu').classList.toggle('active');
        });

        // Add loading animation to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.href && !this.href.includes('#')) {
                    this.classList.add('btn-loading');
                    setTimeout(() => {
                        this.classList.remove('btn-loading');
                    }, 2000);
                }
            });
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Add floating animation to hero elements
        document.querySelectorAll('.floating-element').forEach((el, index) => {
            el.style.animationDelay = `${index * 0.5}s`;
        });
    </script>
</body>
</html>
