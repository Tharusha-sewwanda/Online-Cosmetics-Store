<?php
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductById($id);

if (!$product) {
    header('Location: index.php');
    exit;
}

// Handle "Add to Cart"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = max(1, (int)$_POST['quantity']);
    addToCart($id, $qty);
    header('Location: cart.php');
    exit;
}

$pageTitle = $product['name'];
$assetBase = '';
require_once __DIR__ . '/includes/header.php';
?>

<p><a href="index.php">&larr; Back to shop</a></p>

<div class="product-detail">
    <div>
        <img src="<?php echo h($product['image_url']); ?>" alt="<?php echo h($product['name']); ?>">
    </div>
    <div>
        <span class="product-brand"><?php echo h($product['brand']); ?></span>
        <h1><?php echo h($product['name']); ?></h1>
        <p class="product-meta">
            Category: <?php echo h($product['category']); ?> &nbsp;|&nbsp;
            Skin Type: <?php echo h($product['skin_type']); ?>
            <?php if ($product['shade_variant']): ?>&nbsp;|&nbsp; Variant: <?php echo h($product['shade_variant']); ?><?php endif; ?>
        </p>
        <p class="price"><?php echo formatPrice($product['price']); ?></p>
        <p><?php echo nl2br(h($product['description'])); ?></p>

        <?php if ($product['stock_quantity'] > 0): ?>
            <form method="POST">
                <div class="qty-selector">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo (int)$product['stock_quantity']; ?>">
                    <span class="product-meta"><?php echo (int)$product['stock_quantity']; ?> in stock</span>
                </div>
                <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
            </form>
        <?php else: ?>
            <p class="badge out">Currently out of stock</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
