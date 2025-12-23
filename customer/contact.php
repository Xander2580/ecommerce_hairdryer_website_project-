<?php
require_once '../config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $conn = getDatabaseConnection();
        $name    = $conn->real_escape_string($name);
        $email   = $conn->real_escape_string($email);
        $phone   = $conn->real_escape_string($phone);
        $subject = $conn->real_escape_string($subject);
        $message = $conn->real_escape_string($message);

        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES ('$name', '$email', '$phone', '$subject', '$message')";
        if ($conn->query($sql)) {
            $success = "Thank you! Your message has been sent.";
        } else {
            $error = "Sorry, there was an error. Please try again later.";
        }
    }
}
?>
<?php if ($success): ?>
<script>
  window.onload = function() {
    alert("<?= addslashes($success) ?>");
  };
</script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - StylePro Essentials</title>

  <!-- Main customer styles -->
   <link href="../assests/css/home.css" rel="stylesheet"/>
  <link href="../assests/css/customerdashboard.css" rel="stylesheet"/>

  <style>
    /* Contact-only polish (kept light; works with your grid) */
    .form-label{display:block;margin-bottom:.35rem;font-weight:500;color:#333}
    .form-control,.form-select,textarea.form-control{
      width:100%;padding:.65rem .85rem;border:1px solid #ddd;border-radius:6px;
      font-size:1rem;transition:border .2s, box-shadow .2s
    }
    .form-control:focus,.form-select:focus,textarea.form-control:focus{
      outline:0;border-color:#0046ff;box-shadow:0 0 0 3px rgba(0,70,255,.1)
    }
    .contact-actions{display:flex;gap:.75rem;flex-wrap:wrap}
    .map-box{
      background:#eef2ff;border:1px dashed #cbd5ff;border-radius:12px;height:220px;
      display:flex;align-items:center;justify-content:center;color:#334155
    }
    .faq .q{font-weight:600;margin-bottom:.35rem}
    .faq .a{color:#6c757d;margin-bottom:1rem}
    .contact-info p{margin-bottom:.35rem}
    @media (max-width: 992px){ .contact-actions{flex-direction:column} }
  </style>
</head>
<body>
  <?php include '../includes/navbar.php'; ?>

  <!-- Gradient header (reuses dashboard-header styling) -->
  <div class="dashboard-header">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-md-12">
          <h1 class="display-6 fw-bold">Contact Us</h1>
          <p class="lead mb-0">Questions about hair dryers or straighteners? We’re here to help.</p>
        </div>
      </div>
    </div>
  </div>

  <main class="container-fluid my-4">
    <div class="row">
      <!-- Left: Contact form -->
      <div class="col-lg-8 mb-4">
        <div class="card">
          <div class="card-header"><h5 class="mb-0">Send us a message</h5></div>
          <div class="card-body">
            <form id="contactForm" method="post" novalidate>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="name">Full Name</label>
                  <input id="name" name="name" type="text" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="email">Email</label>
                  <input id="email" name="email" type="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="phone">Phone</label>
                  <input id="phone" name="phone" type="tel" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="subject">Subject</label>
                  <input id="subject" name="subject" type="text" class="form-control" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label" for="message">Message</label>
                  <textarea id="message" name="message" class="form-control" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                <div class="col-12 mb-2">
                  <label class="d-inline-flex align-items-center" style="gap:.5rem;">
                    <input type="checkbox" id="agree" required>
                    <span>I agree to the <a href="#" class="text-muted">Privacy Policy</a>.</span>
                  </label>
                </div>
              </div>

              <div class="contact-actions mt-2">
                <button type="submit" class="btn btn-primary">Send Message</button>
                <a href="../index.php" class="btn btn-light">Back to Home</a>
              </div>
            </form>

            
          </div>
        </div>

        <!-- FAQ -->
        <div class="card mt-3">
          <div class="card-header"><h6 class="mb-0">FAQ</h6></div>
          <div class="card-body faq">
            <div>
              <div class="q">How long does delivery take?</div>
              <div class="a">Standard shipping takes 3–5 days, express 1–2 days.</div>
            </div>
            <div>
              <div class="q">Do you offer returns?</div>
              <div class="a">Yes, 7-day return policy for unused items with original packaging.</div>
            </div>
            <div>
              <div class="q">Which payment methods are accepted?</div>
              <div class="a">Cash on delivery and major debit/credit cards.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Support info -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><h6 class="mb-0">Customer Support</h6></div>
          <div class="card-body contact-info">
            <p><strong>Email:</strong> support@styleproessentials.com</p>
            <p><strong>Phone:</strong> +977-9762261869</p>
            <p><strong>Hours:</strong> Sun–Fri, 9:00 AM – 6:00 PM</p>
            <hr>
            <p class="mb-2"><strong>Store Address</strong></p>
            <p class="mb-2">StylePro Essentials<br>Jadibuti, Kathmandu, Nepal</p>
            <div class="map-box p-0" style="height:220px;overflow:hidden;border-radius:12px;">
              <iframe
                src="https://www.google.com/maps?q=Jadibuti,+Kathmandu,+Nepal&output=embed"
                width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Shared footer -->
  <div data-include="../includes/footer.php"></div>
  <script src="../assests/js/include.js"></script>
</body>
</html>
