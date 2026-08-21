<?php
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= 5")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'Paid'")->fetchColumn();
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div class="admin-topbar">
    <h1>Dashboard</h1>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="label">Total Products</div>
        <div class="value"><?php echo (int)$totalProducts; ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Low Stock (&le;5)</div>
        <div class="value"><?php echo (int)$lowStock; ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Total Orders</div>
        <div class="value"><?php echo (int)$totalOrders; ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Revenue (Paid)</div>
        <div class="value"><?php echo formatPrice($totalRevenue); ?></div>
    </div>
</div>

<h3>Recent Orders</h3>
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($recentOrders)): ?>
            <tr><td colspan="7">No orders yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($recentOrders as $o): ?>
            <tr>
                <td>#<?php echo (int)$o['id']; ?></td>
                <td><?php echo h($o['customer_name']); ?></td>
                <td><?php echo formatPrice($o['total_amount']); ?></td>
                <td><span class="status-pill status-<?php echo strtolower($o['payment_status']); ?>"><?php echo h($o['payment_status']); ?></span></td>
                <td><span class="status-pill status-<?php echo strtolower($o['order_status']); ?>"><?php echo h($o['order_status']); ?></span></td>
                <td><?php echo h(date('d M Y, H:i', strtotime($o['created_at']))); ?></td>
                <td><a href="order_detail.php?id=<?php echo (int)$o['id']; ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
