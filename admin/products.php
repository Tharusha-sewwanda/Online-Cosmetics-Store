<?php
$pageTitle = 'Inventory';
$activeNav = 'products';
require_once __DIR__ . '/includes/admin_header.php';

$pdo = getDBConnection();
$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :s OR brand LIKE :s ORDER BY created_at DESC");
    $stmt->execute([':s' => '%' . $search . '%']);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
}
$products = $stmt->fetchAll();
?>

<div class="admin-topbar">
    <h1>Inventory Management</h1>
</div>

<div class="toolbar">
    <form method="GET" style="display:flex;gap:8px;">
        <input type="text" name="q" placeholder="Search products..." value="<?php echo h($search); ?>"
               style="padding:8px 12px;border:1px solid #e5d9db;border-radius:6px;">
        <button type="submit" class="btn btn-sm">Search</button>
    </form>
    <a href="product_form.php" class="btn">+ Add New Product</a>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr><th>Image</th><th>Name</th><th>Brand</th><th>Category</th><th>Skin Type</th><th>Variant</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if (empty($products)): ?>
            <tr><td colspan="9">No products found.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><img src="<?php echo h($p['image_url']); ?>" alt=""></td>
                <td><?php echo h($p['name']); ?></td>
                <td><?php echo h($p['brand']); ?></td>
                <td><?php echo h($p['category']); ?></td>
                <td><?php echo h($p['skin_type']); ?></td>
                <td><?php echo h($p['shade_variant']); ?></td>
                <td><?php echo formatPrice($p['price']); ?></td>
                <td><?php echo (int)$p['stock_quantity']; ?></td>
                <td class="action-links">
                    <a href="product_form.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
                    <a href="product_delete.php?id=<?php echo (int)$p['id']; ?>"
                       onclick="return confirm('Delete this product? This cannot be undone.');"
                       style="color:#c0392b;">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
