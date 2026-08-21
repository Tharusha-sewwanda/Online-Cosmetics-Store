<?php
require_once __DIR__ . '/includes/functions.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Order Confirmed';
$assetBase = '';
require_once __DIR__ . '/includes/header.php';
?>

<div class="success-box">
    <div class="success-icon">&#10003;</div>
    <h1>Thank you, <?php echo h($order['customer_name']); ?>!</h1>
    <p>Your order <strong>#<?php echo (int)$order['id']; ?></strong> has been placed successfully.</p>
    <p>Payment Status: <span class="status-pill status-<?php echo strtolower($order['payment_status']); ?>"><?php echo h($order['payment_status']); ?></span></p>
    <p>Total Paid: <strong><?php echo formatPrice($order['total_amount']); ?></strong></p>
    <a href="index.php" class="btn" style="margin-top:20px;">Continue Shopping</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
