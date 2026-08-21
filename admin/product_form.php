<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$pdo = getDBConnection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$product = [
    'name' => '', 'brand' => '', 'category' => 'Skincare', 'skin_type' => 'All',
    'shade_variant' => '', 'price' => '', 'stock_quantity' => '', 'image_url' => '', 'description' => ''
];
$errors = [];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $found = $stmt->fetch();
    if (!$found) {
        header('Location: products.php');
        exit;
    }
    $product = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product['name'] = trim($_POST['name'] ?? '');
    $product['brand'] = trim($_POST['brand'] ?? '');
    $product['category'] = $_POST['category'] ?? 'Skincare';
    $product['skin_type'] = $_POST['skin_type'] ?? 'All';
    $product['shade_variant'] = trim($_POST['shade_variant'] ?? '');
    $product['price'] = trim($_POST['price'] ?? '');
    $product['stock_quantity'] = trim($_POST['stock_quantity'] ?? '');
    $product['image_url'] = trim($_POST['image_url'] ?? '');
    $product['description'] = trim($_POST['description'] ?? '');

    if ($product['name'] === '' || $product['brand'] === '') {
        $errors[] = 'Name and brand are required.';
    }
    if (!is_numeric($product['price']) || $product['price'] < 0) {
        $errors[] = 'Please enter a valid price.';
    }
    if (!is_numeric($product['stock_quantity']) || $product['stock_quantity'] < 0) {
        $errors[] = 'Please enter a valid stock quantity.';
    }
    if ($product['image_url'] === '') {
        $product['image_url'] = 'https://picsum.photos/seed/' . urlencode($product['name'] . rand(1,999)) . '/400/400';
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $pdo->prepare("UPDATE products SET name=:name, brand=:brand, category=:category, skin_type=:skin_type,
                shade_variant=:shade_variant, price=:price, stock_quantity=:stock, image_url=:image_url, description=:description
                WHERE id=:id");
            $stmt->execute([
                ':name' => $product['name'], ':brand' => $product['brand'], ':category' => $product['category'],
                ':skin_type' => $product['skin_type'], ':shade_variant' => $product['shade_variant'],
                ':price' => $product['price'], ':stock' => $product['stock_quantity'],
                ':image_url' => $product['image_url'], ':description' => $product['description'], ':id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, brand, category, skin_type, shade_variant, price, stock_quantity, image_url, description)
                VALUES (:name, :brand, :category, :skin_type, :shade_variant, :price, :stock, :image_url, :description)");
            $stmt->execute([
                ':name' => $product['name'], ':brand' => $product['brand'], ':category' => $product['category'],
                ':skin_type' => $product['skin_type'], ':shade_variant' => $product['shade_variant'],
                ':price' => $product['price'], ':stock' => $product['stock_quantity'],
                ':image_url' => $product['image_url'], ':description' => $product['description']
            ]);
        }
        header('Location: products.php');
        exit;
    }
}

$pageTitle = $isEdit ? 'Edit Product' : 'Add Product';
$activeNav = 'products';
require_once __DIR__ . '/includes/admin_header.php';

$categories = ['Skincare', 'Makeup', 'Fragrance', 'Accessories'];
$skinTypes = ['All', 'Oily', 'Dry', 'Combination'];
?>

<div class="admin-topbar">
    <h1><?php echo $isEdit ? 'Edit Product' : 'Add New Product'; ?></h1>
    <a href="products.php" class="btn btn-outline btn-sm">&larr; Back to Inventory</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error"><?php echo h(implode(' ', $errors)); ?></div>
<?php endif; ?>

<div class="form-card" style="max-width:640px;">
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" value="<?php echo h($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="brand">Brand *</label>
                <input type="text" id="brand" name="brand" value="<?php echo h($product['brand']); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?php echo h($c); ?>" <?php echo $product['category'] === $c ? 'selected' : ''; ?>><?php echo h($c); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="skin_type">Skin Type</label>
                <select id="skin_type" name="skin_type">
                    <?php foreach ($skinTypes as $s): ?>
                        <option value="<?php echo h($s); ?>" <?php echo $product['skin_type'] === $s ? 'selected' : ''; ?>><?php echo h($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="shade_variant">Shade / Variant</label>
                <input type="text" id="shade_variant" name="shade_variant" value="<?php echo h($product['shade_variant']); ?>" placeholder="e.g. 50ml, Ruby Red">
            </div>
            <div class="form-group">
                <label for="price">Price (LKR) *</label>
                <input type="number" step="0.01" min="0" id="price" name="price" value="<?php echo h($product['price']); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" min="0" id="stock_quantity" name="stock_quantity" value="<?php echo h($product['stock_quantity']); ?>" required>
            </div>
            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="text" id="image_url" name="image_url" value="<?php echo h($product['image_url']); ?>" placeholder="Leave blank for a mock image">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo h($product['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-block"><?php echo $isEdit ? 'Update Product' : 'Add Product'; ?></button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
