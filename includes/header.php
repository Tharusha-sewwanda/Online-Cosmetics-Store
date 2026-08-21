<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? h($pageTitle) . ' - Bellina Cosmetics' : 'Bellina Cosmetics'; ?></title>
    <link rel="stylesheet" href="<?php echo isset($assetBase) ? $assetBase : ''; ?>assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?php echo isset($assetBase) ? $assetBase : ''; ?>index.php" class="logo">Bellina<span>Cosmetics</span></a>
        <nav class="main-nav">
            <a href="<?php echo isset($assetBase) ? $assetBase : ''; ?>index.php">Shop</a>
            <a href="<?php echo isset($assetBase) ? $assetBase : ''; ?>cart.php" class="cart-link">
                Cart
                <span class="cart-badge" id="cartBadge"><?php echo getCartCount(); ?></span>
            </a>
        </nav>
    </div>
</header>
<main class="container">
