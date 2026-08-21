<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$pdo = getDBConnection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute([':id' => $id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $newOrderStatus = $_POST['order_status'] ?? $order['order_status'];
    $newPaymentStatus = $_POST['payment_status'] ?? $order['payment_status'];
    $upd = $pdo->prepare("UPDATE orders SET order_status = :os, payment_status = :ps WHERE id = :id");
    $upd->execute([':os' => $newOrderStatus, ':ps' => $newPaymentStatus, ':id' => $id]);
    header('Location: order_detail.php?id=' . $id);
    exit;
}

$itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :id");
$itemsStmt->execute([':id' => $id]);
$items = $itemsStmt->fetchAll();

$pageTitle = 'Order #' . $id;
$activeNav = 'orders';
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-topbar">
    <h1>Order #<?php echo (int)$order['id']; ?></h1>
    <a href="orders.php" class="btn btn-outline btn-sm">&larr; Back to Orders</a>
</div>

<div class="form-card" style="max-width:100%;">
    <h3>Customer Details</h3>
    <p><strong>Name:</strong> <?php echo h($order['customer_name']); ?></p>
    <p><strong>Email:</strong> <?php echo h($order['email']); ?></p>
    <p><strong>Phone:</strong> <?php echo h($order['phone']); ?></p>
    <p><strong>Address:</strong> <?php echo h($order['address']); ?></p>
    <p><strong>Placed on:</strong> <?php echo h(date('d M Y, H:i', strtotime($order['created_at']))); ?></p>
</div>

<div class="admin-table-wrap" style="margin-bottom:24px;">
    <table class="admin-table">
        <thead><tr><th>Product</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?php echo h($it['product_name']); ?></td>
                <td><?php echo formatPrice($it['unit_price']); ?></td>
                <td><?php echo (int)$it['quantity']; ?></td>
                <td><?php echo formatPrice($it['subtotal']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" style="text-align:right;"><strong>Total</strong></td>
            <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
        </tr>
        </tbody>
    </table>
</div>

<div class="form-card">
    <h3>Update Status</h3>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="payment_status">Payment Status</label>
                <select id="payment_status" name="payment_status">
                    <?php foreach (['Pending', 'Paid', 'Failed'] as $s): ?>
                        <option value="<?php echo h($s); ?>" <?php echo $order['payment_status'] === $s ? 'selected' : ''; ?>><?php echo h($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="order_status">Order Status</label>
                <select id="order_status" name="order_status">
                    <?php foreach (['Processing', 'Shipped', 'Delivered', 'Cancelled'] as $s): ?>
                        <option value="<?php echo h($s); ?>" <?php echo $order['order_status'] === $s ? 'selected' : ''; ?>><?php echo h($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" name="update_status" class="btn">Save Changes</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
