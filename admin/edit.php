<?php
// edit.php - Edit Customer Details
require_once '../config/database.php';
requireAdminLogin();

$conn = getDatabaseConnection();

// --- Validate and fetch customer ---
if (!isset($_GET['customer_id']) || !ctype_digit($_GET['customer_id'])) {
    $_SESSION['msg'] = "No valid customer selected.";
    header("Location: customermanagement.php");
    exit();
}
$customer_id = (int) $_GET['customer_id'];

$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $_SESSION['msg'] = "Customer not found.";
    header("Location: customermanagement.php");
    exit();
}
$customer = $result->fetch_assoc();

// --- Handle update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_customer'])) {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $status     = $_POST['status'];

    $stmt = $conn->prepare(
        "UPDATE customers SET first_name=?, last_name=?, email=?, phone=?, status=? WHERE customer_id=?"
    );
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $status, $customer_id);

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Customer updated successfully.";
        header("Location: customermanagement.php");
        exit();
    } else {
        $_SESSION['msg'] = "Customer update failed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/admindashboard.css" rel="stylesheet">
    <style>
        .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
        .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; }
        .form-group input, .form-group select {
            width:100%; padding:10px 12px; border:1px solid #ced4da; border-radius:8px; background:#fbfcfe;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="admin-main-content">
        <div class="dashboard-header">
            <div>
                <h1>Edit Customer</h1>
                <p>Edit the customer details below</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Update Customer Details</h6>
            </div>
            <div class="card-body">
                <form action="edit.php?customer_id=<?php echo $customer_id; ?>" method="POST">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name"
                                   value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name"
                                   value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>
                        </div>
                    </div>
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                   value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="active" <?php if ($customer['status']==='active') echo 'selected'; ?>>Active</option>
                                <option value="inactive" <?php if ($customer['status']==='inactive') echo 'selected'; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:20px;">
                        <button type="submit" name="edit_customer" class="btn btn-primary">Update Customer</button>
                        <a href="customermanagement.php" class="btn btn-secondary">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.admin-main-content -->
</div>
</body>
</html>
