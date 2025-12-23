<?php
// admin/report&analytics.php — Reports & Analytics
require_once '../config/database.php';
requireAdminLogin();

$user = getCurrentUser();
$conn = getDatabaseConnection();

/* ---------- Date Range ---------- */
$today = new DateTime('today');
$defaultFrom = (clone $today)->modify('-29 days'); // last 30 days
$from = isset($_GET['from']) && $_GET['from'] !== '' ? DateTime::createFromFormat('Y-m-d', $_GET['from']) : $defaultFrom;
$to   = isset($_GET['to']) && $_GET['to'] !== '' ? DateTime::createFromFormat('Y-m-d', $_GET['to']) : $today;

$fromStr = $from ? $from->format('Y-m-d 00:00:00') : $defaultFrom->format('Y-m-d 00:00:00');
$toStr   = $to   ? $to->format('Y-m-d 23:59:59')   : $today->format('Y-m-d 23:59:59');

/* ---------- KPIs ---------- */
$kpi = ['revenue'=>0.0, 'orders'=>0, 'aov'=>0.0, 'active_products'=>0, 'ordering_customers'=>0];

// revenue & orders
$sql = "
    SELECT COALESCE(SUM(final_amount),0) as revenue, COUNT(*) as orders
    FROM orders
    WHERE payment_status='paid' AND created_at BETWEEN '$fromStr' AND '$toStr'
";
$res = $conn->query($sql);
if ($row = $res->fetch_assoc()) {
    $kpi['revenue'] = (float)$row['revenue'];
    $kpi['orders']  = (int)$row['orders'];
    $kpi['aov']     = $kpi['orders'] > 0 ? $kpi['revenue'] / $kpi['orders'] : 0.0;
}

// active products
$res = $conn->query("SELECT COUNT(*) AS c FROM products WHERE status='active'");
if ($res && $r=$res->fetch_assoc()) $kpi['active_products'] = (int)$r['c'];

// customers who placed orders in range
$sql = "
    SELECT COUNT(DISTINCT customer_id) AS c
    FROM orders
    WHERE created_at BETWEEN '$fromStr' AND '$toStr'
";
$res = $conn->query($sql);
if ($row = $res->fetch_assoc()) $kpi['ordering_customers'] = (int)$row['c'];

/* ---------- Daily Trend ---------- */
$daily = [];
$sql = "
    SELECT DATE(created_at) AS d, COUNT(*) AS orders, COALESCE(SUM(final_amount),0) AS revenue
    FROM orders
    WHERE payment_status='paid' AND created_at BETWEEN '$fromStr' AND '$toStr'
    GROUP BY DATE(created_at)
    ORDER BY d ASC
";
$res = $conn->query($sql);
$maxRevenue = 0;
$maxOrders  = 0;
while ($row = $res->fetch_assoc()) {
    $d = $row['d'];
    $daily[$d] = ['orders'=>(int)$row['orders'], 'revenue'=>(float)$row['revenue']];
    if ($daily[$d]['revenue'] > $maxRevenue) $maxRevenue = $daily[$d]['revenue'];
    if ($daily[$d]['orders']  > $maxOrders)  $maxOrders  = $daily[$d]['orders'];
}

// Fill missing days
$cursor = DateTime::createFromFormat('Y-m-d H:i:s', $fromStr);
$end    = DateTime::createFromFormat('Y-m-d H:i:s', $toStr);
while ($cursor <= $end) {
    $key = $cursor->format('Y-m-d');
    if (!isset($daily[$key])) $daily[$key] = ['orders'=>0,'revenue'=>0.0];
    $cursor->modify('+1 day');
}

function barWidth($value, $max) {
    if ($max <= 0) return 0;
    $w = ($value / $max) * 100;
    return max(2, min(100, $w));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports & Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assests/css/home.css" rel="stylesheet">
    <link href="../assests/css/admindashboard.css" rel="stylesheet">
    <style>
        .filters { display:grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap:12px; }
        @media (max-width: 900px){ .filters{grid-template-columns:1fr 1fr;} }
        @media (max-width: 600px){ .filters{grid-template-columns:1fr;} }
        .kpi-row { display:flex; gap:24px; flex-wrap:wrap; margin:20px 0; }
        .kpi { flex:1 1 200px; background:#f9f9fb; border-radius:12px; padding:18px; box-shadow:0 3px 8px rgba(0,0,0,.05); }
        .kpi .label { font-size:.9rem; color:#777; text-transform:uppercase; font-weight:600; }
        .kpi .value { font-size:1.6rem; font-weight:700; color:#333; margin-top:6px; }
        .bar { height:10px; border-radius:6px; background:#e9eef8; }
        .bar span { display:block; height:100%; border-radius:6px; background:linear-gradient(90deg,#5e72e4,#ff3e78); }
    </style>
</head>
<body>
<div class="container-fluid">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="text-center mb-4">
            <div style="width:60px;height:60px;background:#007bff;border-radius:50%;margin:0 auto 1rem;"></div>
            <h6>Admin Panel</h6>
            <small class="text-muted">Welcome, <?php echo htmlspecialchars($user['name'] ?? $user['username'] ?? 'Admin'); ?></small>
        </div>
        <ul class="nav">
            <li><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <li><a class="nav-link" href="productmanagement.php">Product Management</a></li>
            <li><a class="nav-link" href="ordermanagement.php">Order Management</a></li>
            <li><a class="nav-link" href="customermanagement.php">Customer Management</a></li>
            <li><a class="nav-link" href="usermanagement.php">User Management</a></li>
            <li><a class="nav-link active" href="report&analytics.php">Reports & Analytics</a></li>
            <li><a class="nav-link" href="../index.php" target="_blank">View Website</a></li>
            <li><a class="nav-link text-danger" href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main -->
    <div class="admin-main-content">
        <div class="dashboard-header">
            <h1>Reports & Analytics</h1>
            <p>Track sales performance and key metrics</p>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header"><h6>Date Range</h6></div>
            <div class="card-body">
                <form class="filters" method="GET" action="report&analytics.php">
                    <div><label>From</label><input type="date" name="from" value="<?php echo htmlspecialchars(($from ?? $defaultFrom)->format('Y-m-d')); ?>"></div>
                    <div><label>To</label><input type="date" name="to" value="<?php echo htmlspecialchars(($to ?? $today)->format('Y-m-d')); ?>"></div>
                    <div style="align-self:end;"><button class="btn btn-primary">Apply</button></div>
                    <div style="align-self:end;"><a class="btn btn-outline-success" href="report&analytics.php?from=<?php echo date('Y-m-d', strtotime('-6 days')); ?>&to=<?php echo date('Y-m-d'); ?>">Last 7 Days</a></div>
                    <div style="align-self:end;"><a class="btn btn-outline-success" href="report&analytics.php?from=<?php echo date('Y-m-d', strtotime('-29 days')); ?>&to=<?php echo date('Y-m-d'); ?>">Last 30 Days</a></div>
                </form>
            </div>
        </div>

        <!-- KPIs -->
        <div class="kpi-row">
            <div class="kpi"><div class="label">Total Revenue</div><div class="value"><?php echo number_format($kpi['revenue'], 2); ?></div></div>
            <div class="kpi"><div class="label">Total Orders</div><div class="value"><?php echo number_format($kpi['orders']); ?></div></div>
            <div class="kpi"><div class="label">Avg Order Value</div><div class="value"><?php echo number_format($kpi['aov'], 2); ?></div></div>
            <div class="kpi"><div class="label">Active Products</div><div class="value"><?php echo number_format($kpi['active_products']); ?></div></div>
            <div class="kpi"><div class="label">Customers</div><div class="value"><?php echo number_format($kpi['ordering_customers']); ?></div></div>
        </div>

        <!-- Daily Trend Table -->
        <div class="card">
            <div class="card-header"><h6>Daily Revenue & Orders</h6></div>
            <div class="card-body">
                <?php if (!empty($daily)): ?>
                <table class="table table-hover">
                    <thead><tr><th>Date</th><th>Revenue</th><th>Amount</th><th>Orders</th><th>Count</th></tr></thead>
                    <tbody>
                    <?php foreach ($daily as $day => $vals): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($day); ?></td>
                            <td><div class="bar"><span style="width:<?php echo barWidth($vals['revenue'],$maxRevenue); ?>%"></span></div></td>
                            <td><strong><?php echo number_format($vals['revenue'],2); ?></strong></td>
                            <td><div class="bar"><span style="width:<?php echo barWidth($vals['orders'],$maxOrders); ?>%; background:#ff3e78;"></span></div></td>
                            <td><strong><?php echo $vals['orders']; ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?><p class="text-muted">No data for this period.</p><?php endif; ?>
            </div>
        </div>

        <!-- Inventory Snapshot -->
        <div class="card">
            <div class="card-header"><h6>Inventory Snapshot (Low Stock)</h6></div>
            <div class="card-body">
                <?php
                $low = $conn->query("SELECT product_name, stock_quantity FROM products WHERE status='active' AND stock_quantity <= 5 ORDER BY stock_quantity ASC LIMIT 10");
                if ($low && $low->num_rows > 0): ?>
                    <table class="table">
                        <thead><tr><th>Product</th><th>Stock</th></tr></thead>
                        <tbody>
                        <?php while($r = $low->fetch_assoc()): ?>
                            <tr><td><?php echo htmlspecialchars($r['product_name']); ?></td><td><span class="badge bg-warning"><?php echo (int)$r['stock_quantity']; ?></span></td></tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?><p class="text-success">All active products are sufficiently stocked.</p><?php endif; ?>
            </div>
        </div>

    </div><!-- /.admin-main-content -->
</div>
</body>
</html>
