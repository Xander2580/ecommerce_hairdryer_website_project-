<?php
// blog.php
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Blog - StylePro Essentials</title>
  <link href="../assests/css/home.css" rel="stylesheet"/>
  <link href="../assests/css/customerdashboard.css" rel="stylesheet"/>
  <link href="../assests/css/profile.css" rel="stylesheet"/>
  <style>
    /* Blog page specific styles */
    .blog-hero {
      background: linear-gradient(135deg, #001bb7 0%, #0046ff 100%);
      color: #fff;
      padding: 60px 0 40px 0;
      margin-bottom: 0;
    }
    .blog-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 12px #001bb71a;
      margin-bottom: 32px;
      overflow: hidden;
      transition: box-shadow .2s;
    }
    .blog-card:hover {
      box-shadow: 0 4px 24px #001bb733;
    }
    .blog-card-img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }
    .blog-card-body {
      padding: 24px;
    }
    .blog-card-title {
      font-size: 1.35rem;
      font-weight: 600;
      margin-bottom: .5rem;
    }
    .blog-card-meta {
      color: #6c757d;
      font-size: .95rem;
      margin-bottom: .75rem;
    }
    .blog-card-text {
      color: #444;
      margin-bottom: 1rem;
    }
    .blog-readmore {
      color: #0046ff;
      font-weight: 500;
      text-decoration: none;
      transition: color .2s;
    }
    .blog-readmore:hover {
      color: #001bb7;
      text-decoration: underline;
    }
    @media (max-width: 992px) {
      .blog-card-img { height: 160px; }
    }
  </style>
</head>
<body>
  <?php include '../includes/navbar.php'; ?>

  <section class="blog-hero">
    <div class="container-fluid">
      <h1 class="display-5 fw-bold mb-2">StylePro Blog</h1>
      <p class="lead mb-0" style="max-width:600px;">Tips, trends, and inspiration for your best hair days—straight from the StylePro Essentials team.</p>
    </div>
  </section>

  <main class="container-fluid py-5">
    <div class="row">
      <!-- Blog posts grid -->
      <div class="col-lg-8">
        <!-- Blog Post 1 -->
        <div class="blog-card">
          <img src="../assests/images/blog/haircare_tips.jpg" alt="Hair Care Tips" class="blog-card-img">
          <div class="blog-card-body">
            <div class="blog-card-title">5 Essential Hair Care Tips for Healthy, Shiny Hair</div>
            <div class="blog-card-meta"><i class="fa-regular fa-calendar"></i> Sep 10, 2025 &nbsp; | &nbsp; <i class="fa-regular fa-user"></i> StylePro Team</div>
            <div class="blog-card-text">
              Discover the secrets to maintaining beautiful, healthy hair all year round. From choosing the right products to daily routines, these tips will transform your hair care game.
            </div>
            <a href="#" class="blog-readmore">Read More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <!-- Blog Post 2 -->
        <div class="blog-card">
          <img src="../assests/images/blog/styling_tools.jpg" alt="Best Styling Tools" class="blog-card-img">
          <div class="blog-card-body">
            <div class="blog-card-title">Choosing the Best Styling Tools for Your Hair Type</div>
            <div class="blog-card-meta"><i class="fa-regular fa-calendar"></i> Aug 28, 2025 &nbsp; | &nbsp; <i class="fa-regular fa-user"></i> StylePro Experts</div>
            <div class="blog-card-text">
              Not all styling tools are created equal! Learn how to pick the perfect hair dryer, straightener, or curler for your unique hair needs and achieve salon-quality results at home.
            </div>
            <a href="#" class="blog-readmore">Read More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <!-- Blog Post 3 -->
        <div class="blog-card">
          <img src="../assests/images/blog/salon_trends.jpg" alt="Salon Trends" class="blog-card-img">
          <div class="blog-card-body">
            <div class="blog-card-title">Top 2025 Salon Trends You Can Try at Home</div>
            <div class="blog-card-meta"><i class="fa-regular fa-calendar"></i> Aug 15, 2025 &nbsp; | &nbsp; <i class="fa-regular fa-user"></i> Guest Stylist</div>
            <div class="blog-card-text">
              Stay ahead of the curve with the latest hair trends from leading salons. We break down the hottest looks and how you can recreate them with StylePro Essentials.
            </div>
            <a href="#" class="blog-readmore">Read More <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
        <!-- Add more blog posts here as needed -->
      </div>
      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-header"><h6 class="mb-0">Categories</h6></div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li><a href="#" class="text-primary">Hair Care</a></li>
              <li><a href="#" class="text-primary">Styling Tools</a></li>
              <li><a href="#" class="text-primary">Salon Trends</a></li>
              <li><a href="#" class="text-primary">How-To Guides</a></li>
              <li><a href="#" class="text-primary">Product Reviews</a></li>
            </ul>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><h6 class="mb-0">About Our Blog</h6></div>
          <div class="card-body">
            <p>Get expert advice, inspiration, and the latest news from the StylePro Essentials team. Whether you’re a pro or just love great hair, our blog is for you!</p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php'; ?>
  <script src="../assests/js/include.js"></script>
</body>
</html>