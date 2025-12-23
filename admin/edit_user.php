<?php
// edit_user.php - Edit User Details
require_once '../config/database.php';
requireAdminLogin();

$conn = getDatabaseConnection();

// --- Validate and fetch user ---
if (!isset($_GET['user_id']) || !ctype_digit($_GET['user_id'])) {
    $_SESSION['msg'] = "No valid user selected.";
    header("Location: usermanagement.php");
    exit();
}
$user_id = (int) $_GET['user_id'];

$stmt = $conn->prepare("SELECT user_id, full_name, username, email, role, status, created_at FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    $_SESSION['msg'] = "User not found.";
    header("Location: usermanagement.php");
    exit();
}
$userRow = $res->fetch_assoc();

// --- Handle update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $role      = trim($_POST['role'] ?? 'staff');
    $status    = trim($_POST['status'] ?? 'inactive');

    if ($full_name !== '' && $username !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $upd = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, role = ?, status = ? WHERE user_id = ?");
        $upd->bind_param("sssssi", $full_name, $username, $email, $role, $status, $user_id);
        if ($upd->execute()) {
            $_SESSION['msg'] = "User updated successfully.";
            header("Location: usermanagement.php");
            exit();
        } else {
            $_SESSION['msg'] = "User update failed.";
        }
    } else {
        $_SESSION['msg'] = "Please fill out all fields correctly.";
    }

    // Refresh current values so the form re-renders with latest attempts
    $userRow = [
        'user_id'   => $user_id,
        'full_name' => $full_name,
        'username'  => $username,
        'email'     => $email,
        'role'      => $role,
        'status'    => $status,
        'created_at'=> $userRow['created_at'] ?? ''
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/admindashboard.css" rel="stylesheet">
    <style>
        .form-grid { display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px; }
        .form-grid-3 { display:grid; grid-template-columns: 1fr 1fr 1fr; gap:20px; margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
        .form-group input, .form-group select {
            width:100%; padding:10px 12px; border:1px solid #ced4da; border-radius:8px; background:#fbfcfe;
        }
        @media (max-width: 900px) {
            .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="admin-main-content">
        <div class="dashboard-header">
            <div>
                <h1>Edit User</h1>
                <p>Edit the user details below</p>
            </div>
        </div>

        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-info" style="border-radius:12px;">
                <?php echo htmlspecialchars($_SESSION['msg']); unset($_SESSION['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h6>Update User Details</h6>
            </div>
            <div class="card-body">
                <form action="edit_user.php?user_id=<?php echo (int)$userRow['user_id']; ?>" method="POST" novalidate>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name"
                                   value="<?php echo htmlspecialchars($userRow['full_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username"
                                   value="<?php echo htmlspecialchars($userRow['username']); ?>" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                   value="<?php echo htmlspecialchars($userRow['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="admin" <?php echo ($userRow['role'] === 'admin') ? 'selected':''; ?>>Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="active" <?php echo ($userRow['status'] === 'active') ? 'selected':''; ?>>Active</option>
                                <option value="inactive" <?php echo ($userRow['status'] === 'inactive') ? 'selected':''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Created At</label>
                            <input type="text" value="<?php echo htmlspecialchars($userRow['created_at']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <!-- empty to align grid nicely -->
                        </div>
                    </div>

                    <div style="display:flex; justify-content:space-between; margin-top:20px;">
                        <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
                        <a href="usermanagement.php" class="btn btn-secondary">Go Back</a>
                    </div>
                </form>
            </div>
        </div>

    </div><!-- /.admin-main-content -->
</div><!-- /.container-fluid -->
</body>
</html>
