<?php
$pageTitle = 'Orders';
$activeNav = 'orders';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();
$statusFilter = $_GET['status'] ?? '';

if ($statusFilter !== '') {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_status = :s ORDER BY created_at DESC");
    $stmt->execute([':s' => $statusFilter]);
} else {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
}
$orders = $stmt->fetchAll();
?>

<div class="admin-topbar">
    <h1>Order Management</h1>
</div>

<div class="toolbar">
    <form method="GET" style="display:flex;gap:8px;">
        <select name="status" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #e5d9db;border-radius:6px;">
            <option value="">All Statuses</option>
            <?php foreach (['Processing', 'Shipped', 'Delivered', 'Cancelled'] as $s): ?>
                <option value="<?php echo h($s); ?>" <?php echo $statusFilter === $s ? 'selected' : ''; ?>><?php echo h($s); ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr><th>Order #</th><th>Customer</th><th>Email</th><th>Total</th><th>Payment</th><th>Order Status</th><th>Date</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($orders)): ?>
            <tr><td colspan="8">No orders found.</td></tr>
        <?php endif; ?>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo (int)$o['id']; ?></td>
                <td><?php echo h($o['customer_name']); ?></td>
                <td><?php echo h($o['email']); ?></td>
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
