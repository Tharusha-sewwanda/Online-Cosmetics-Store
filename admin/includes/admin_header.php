<?php
/**
 * Shared header/sidebar for all admin pages.
 * Expects $pageTitle and $activeNav to be set before include.
 */
require_once __DIR__ . '/../../includes/auth.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? h($pageTitle) . ' - Admin' : 'Admin'; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="brand">Bellina Admin</div>
        <a href="dashboard.php" class="<?php echo ($activeNav ?? '') === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
        <a href="products.php" class="<?php echo ($activeNav ?? '') === 'products' ? 'active' : ''; ?>">Inventory</a>
        <a href="orders.php" class="<?php echo ($activeNav ?? '') === 'orders' ? 'active' : ''; ?>">Orders</a>
        <a href="../index.php" target="_blank">View Store &#8599;</a>
        <a href="logout.php">Logout (<?php echo h($_SESSION['admin_username'] ?? ''); ?>)</a>
    </aside>
    <div class="admin-main">
