<?php
$pageTitle = 'Shop';
$assetBase = '';
require_once __DIR__ . '/includes/functions.php';

$products = getProducts();
$categories = ['Skincare', 'Makeup', 'Fragrance', 'Accessories'];
$skinTypes = ['Oily', 'Dry', 'Combination'];

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <h1>Discover Your Everyday Beauty Essentials</h1>
    <p>Browse skincare, makeup, fragrance & accessories curated for every skin type.</p>
</div>

<form id="filterForm" class="filter-bar">
    <input type="text" id="searchInput" placeholder="Search by product name or brand...">
    <select id="categorySelect">
        <option value="">All Categories</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?php echo h($c); ?>"><?php echo h($c); ?></option>
        <?php endforeach; ?>
    </select>
    <select id="skinTypeSelect">
        <option value="">All Skin Types</option>
        <?php foreach ($skinTypes as $s): ?>
            <option value="<?php echo h($s); ?>"><?php echo h($s); ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Search</button>
    <button type="button" id="clearFiltersBtn" class="btn-clear">Clear</button>
</form>

<div class="product-grid" id="productGrid">
    <?php if (empty($products)): ?>
        <p class="empty-state">No products available right now. Please check back soon.</p>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
            <div class="product-card">
                <a href="product.php?id=<?php echo (int)$p['id']; ?>">
                    <img src="<?php echo h($p['image_url']); ?>" alt="<?php echo h($p['name']); ?>">
                </a>
                <div class="product-info">
                    <span class="product-brand"><?php echo h($p['brand']); ?></span>
                    <a href="product.php?id=<?php echo (int)$p['id']; ?>">
                        <span class="product-name"><?php echo h($p['name']); ?></span>
                    </a>
                    <span class="product-meta">
                        <?php echo h($p['category']); ?><?php echo $p['shade_variant'] ? ' · ' . h($p['shade_variant']) : ''; ?>
                    </span>
                    <span class="badge <?php echo $p['stock_quantity'] <= 0 ? 'out' : ''; ?>">
                        <?php echo $p['stock_quantity'] <= 0 ? 'Out of stock' : 'Skin type: ' . h($p['skin_type']); ?>
                    </span>
                    <span class="product-price"><?php echo formatPrice($p['price']); ?></span>
                    <a class="btn btn-block" href="product.php?id=<?php echo (int)$p['id']; ?>">View Product</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
