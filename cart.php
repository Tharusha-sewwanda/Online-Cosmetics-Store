<?php
require_once __DIR__ . '/includes/functions.php';

// AJAX update endpoint (used by main.js)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $productId = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
    updateCartQuantity($productId, $quantity);
    echo json_encode(['success' => true, 'count' => getCartCount()]);
    exit;
}

if (isset($_GET['remove'])) {
    removeFromCart($_GET['remove']);
    header('Location: cart.php');
    exit;
}

$pageTitle = 'Your Cart';
$assetBase = '';
require_once __DIR__ . '/includes/header.php';

$cart = getCartDetails();
?>

<h1>Your Shopping Cart</h1>

<?php if (empty($cart['items'])): ?>
    <div class="empty-state">
        <p>Your cart is empty.</p>
        <a href="index.php" class="btn">Continue Shopping</a>
    </div>
<?php else: ?>
    <table class="cart-table" id="cartTable">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cart['items'] as $item): ?>
            <tr>
                <td>
                    <span class="cart-item-name">
                        <img src="<?php echo h($item['product']['image_url']); ?>" alt="">
                        <?php echo h($item['product']['name']); ?>
                        <?php if ($item['product']['shade_variant']): ?> (<?php echo h($item['product']['shade_variant']); ?>)<?php endif; ?>
                    </span>
                </td>
                <td><?php echo formatPrice($item['product']['price']); ?></td>
                <td>
                    <input type="number" min="0" max="<?php echo (int)$item['product']['stock_quantity']; ?>"
                        class="cart-qty-input" value="<?php echo (int)$item['quantity']; ?>"
                        data-product-id="<?php echo (int)$item['product']['id']; ?>">
                </td>
                <td><?php echo formatPrice($item['subtotal']); ?></td>
                <td>
                    <button type="button" class="btn btn-outline btn-sm remove-item-btn"
                        data-product-id="<?php echo (int)$item['product']['id']; ?>">Remove</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="cart-summary">
        <div class="row total">
            <span>Total</span>
            <span><?php echo formatPrice($cart['total']); ?></span>
        </div>
        <a href="checkout.php" class="btn btn-block" style="margin-top:16px;">Proceed to Checkout</a>
        <a href="index.php" class="btn btn-outline btn-block" style="margin-top:10px;">Continue Shopping</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
