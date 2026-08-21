<?php
require_once __DIR__ . '/includes/functions.php';

$cart = getCartDetails();
if (empty($cart['items'])) {
    header('Location: cart.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'Card';

    if ($name === '' || $email === '' || $phone === '' || $address === '') {
        $errors[] = 'Please fill in all required fields.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address.';
    }

    // Mock payment gateway: card payments are simulated as always successful
    // once basic card fields are present. In a real system this step would
    // call out to a payment provider (e.g. Stripe/PayHere) via their API.
    if ($paymentMethod === 'Card') {
        $cardNumber = trim($_POST['card_number'] ?? '');
        $cardExpiry = trim($_POST['card_expiry'] ?? '');
        $cardCvv = trim($_POST['card_cvv'] ?? '');
        if ($cardNumber === '' || $cardExpiry === '' || $cardCvv === '') {
            $errors[] = 'Please provide your card details to complete payment.';
        }
    }

    if (empty($errors)) {
        $pdo = getDBConnection();
        try {
            $pdo->beginTransaction();

            $paymentStatus = ($paymentMethod === 'Card') ? 'Paid' : 'Pending';

            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, email, phone, address, total_amount, payment_method, payment_status, order_status)
                                    VALUES (:name, :email, :phone, :address, :total, :method, :pstatus, 'Processing')");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':total' => $cart['total'],
                ':method' => $paymentMethod,
                ':pstatus' => $paymentStatus,
            ]);
            $orderId = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, subtotal)
                                        VALUES (:order_id, :product_id, :product_name, :unit_price, :quantity, :subtotal)");
            $stockStmt = $pdo->prepare("UPDATE products SET stock_quantity = GREATEST(stock_quantity - :qty, 0) WHERE id = :id");

            foreach ($cart['items'] as $item) {
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product']['id'],
                    ':product_name' => $item['product']['name'],
                    ':unit_price' => $item['product']['price'],
                    ':quantity' => $item['quantity'],
                    ':subtotal' => $item['subtotal'],
                ]);
                $stockStmt->execute([
                    ':qty' => $item['quantity'],
                    ':id' => $item['product']['id'],
                ]);
            }

            $pdo->commit();
            clearCart();
            header('Location: order_success.php?order_id=' . $orderId);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Something went wrong while placing your order. Please try again.';
        }
    }
}

$pageTitle = 'Checkout';
$assetBase = '';
require_once __DIR__ . '/includes/header.php';
?>

<h1>Checkout</h1>

<div class="form-card">
    <h3>Order Summary</h3>
    <?php foreach ($cart['items'] as $item): ?>
        <div class="row" style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;">
            <span><?php echo h($item['product']['name']); ?> &times; <?php echo (int)$item['quantity']; ?></span>
            <span><?php echo formatPrice($item['subtotal']); ?></span>
        </div>
    <?php endforeach; ?>
    <div class="row total" style="display:flex;justify-content:space-between;">
        <span>Total</span>
        <span><?php echo formatPrice($cart['total']); ?></span>
    </div>
</div>

<div class="form-card">
    <h3>Shipping & Contact Details</h3>

    <div id="checkoutError" class="alert alert-error" style="display:none;"></div>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><?php echo h(implode(' ', $errors)); ?></div>
    <?php endif; ?>

    <form id="checkoutForm" method="POST">
        <div class="form-group">
            <label for="customerName">Full Name *</label>
            <input type="text" id="customerName" name="customer_name" value="<?php echo h($_POST['customer_name'] ?? ''); ?>" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo h($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone *</label>
                <input type="text" id="phone" name="phone" value="<?php echo h($_POST['phone'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label for="address">Delivery Address *</label>
            <textarea id="address" name="address" rows="3" required><?php echo h($_POST['address'] ?? ''); ?></textarea>
        </div>

        <h3>Payment Method</h3>
        <div class="payment-methods">
            <label><input type="radio" name="payment_method" value="Card" checked onclick="document.getElementById('cardFields').style.display='block'"> Card Payment</label>
            <label><input type="radio" name="payment_method" value="Cash on Delivery" onclick="document.getElementById('cardFields').style.display='none'"> Cash on Delivery</label>
        </div>

        <div id="cardFields">
            <div class="form-group">
                <label for="cardNumber">Card Number</label>
                <input type="text" id="cardNumber" name="card_number" placeholder="4242 4242 4242 4242" maxlength="19">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="cardExpiry">Expiry (MM/YY)</label>
                    <input type="text" id="cardExpiry" name="card_expiry" placeholder="12/28">
                </div>
                <div class="form-group">
                    <label for="cardCvv">CVV</label>
                    <input type="text" id="cardCvv" name="card_cvv" placeholder="123" maxlength="4">
                </div>
            </div>
            <p class="product-meta">This is a simulated payment gateway for demo purposes — no real transaction is processed.</p>
        </div>

        <button type="submit" class="btn btn-block">Place Order</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
