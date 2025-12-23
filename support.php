<?php
require_once 'config/database.php';
requireCustomerLogin();

$customer = getCurrentCustomer();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($subject) || empty($message)) {
        $error = "Please fill in all fields.";
    } else {
        $conn = getDatabaseConnection();
        $customer_id = $_SESSION['customer_id'];
        $subject_esc = mysqli_real_escape_string($conn, $subject);
        $message_esc = mysqli_real_escape_string($conn, $message);

        $sql = "INSERT INTO support_tickets (customer_id, subject, message, status, created_at)
                VALUES ($customer_id, '$subject_esc', '$message_esc', 'open', NOW())";
        if (mysqli_query($conn, $sql)) {
            $success = "Your support request has been submitted. Our team will contact you soon.";
        } else {
            $error = "Failed to submit your request. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support - StylePro Essentials</title>
    <!-- Use the same CSS as dashboard.php -->
    <link href="assests/css/home.css" rel="stylesheet">
    <link href="assests/css/customerdashboard.css" rel="stylesheet">
    <link href="assests/css/profile.css" rel="stylesheet">
    <style>
        .support-container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 32px; }
        .support-container h2 { margin-bottom: 20px; }
        .form-group { margin-bottom: 18px; }
        .form-control { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; }
        .btn-support { background: #007bff; color: #fff; border: none; padding: 10px 28px; border-radius: 4px; cursor: pointer; }
        .alert { padding: 10px 16px; border-radius: 4px; margin-bottom: 18px; }
        .alert-success { background: #e6f9e6; color: #2d7a2d; }
        .alert-danger { background: #ffeaea; color: #b30000; }
    </style>
</head>
<body>
    <div data-include="includes/navbar.php"></div>
    <div class="support-container">
        <h2>Contact Customer Support</h2>
        <p>If you have any questions, issues, or need help, please fill out the form below. Our support team will get back to you as soon as possible.</p>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="subject">Subject *</label>
                <input type="text" id="subject" name="subject" class="form-control" required value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn-support">Submit Request</button>
        </form>
        <div style="margin-top:24px;">
            <a href="customer/dashboard.php">&larr; Back to Dashboard</a>
        </div>
    </div>
    <div data-include="includes/footer.php"></div>
    <script src="assests/js/include.js"></script>
</body>
</html>