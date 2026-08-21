<?php
/**
 * Shared helper functions used across the public site.
 */
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Escape output safely for HTML */
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** Format a price as LKR currency */
function formatPrice($price) {
    return 'Rs. ' . number_format((float)$price, 2);
}

/**
 * Fetch products with optional search/filter criteria.
 * @param array $filters keys: search, category, skin_type
 */
function getProducts($filters = []) {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];

    if (!empty($filters['search'])) {
        $sql .= " AND (name LIKE :search OR brand LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    if (!empty($filters['category'])) {
        $sql .= " AND category = :category";
        $params[':category'] = $filters['category'];
    }
    if (!empty($filters['skin_type'])) {
        $sql .= " AND (skin_type = :skin_type OR skin_type = 'All')";
        $params[':skin_type'] = $filters['skin_type'];
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProductById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/* ---------------- Cart (session based) ---------------- */

function getCart() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

function addToCart($productId, $quantity = 1) {
    $cart = getCart();
    $productId = (int)$productId;
    $quantity = max(1, (int)$quantity);

    if (isset($cart[$productId])) {
        $cart[$productId] += $quantity;
    } else {
        $cart[$productId] = $quantity;
    }
    $_SESSION['cart'] = $cart;
}

function updateCartQuantity($productId, $quantity) {
    $cart = getCart();
    $productId = (int)$productId;
    $quantity = (int)$quantity;

    if ($quantity <= 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = $quantity;
    }
    $_SESSION['cart'] = $cart;
}

function removeFromCart($productId) {
    $cart = getCart();
    unset($cart[(int)$productId]);
    $_SESSION['cart'] = $cart;
}

function clearCart() {
    $_SESSION['cart'] = [];
}

/** Returns array of cart line items joined with product data, plus total */
function getCartDetails() {
    $cart = getCart();
    $items = [];
    $total = 0.0;

    if (!empty($cart)) {
        $pdo = getDBConnection();
        $ids = array_map('intval', array_keys($cart));
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll();
        $productsById = [];
        foreach ($products as $p) {
            $productsById[$p['id']] = $p;
        }

        foreach ($cart as $pid => $qty) {
            if (!isset($productsById[$pid])) continue;
            $p = $productsById[$pid];
            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
            $items[] = [
                'product' => $p,
                'quantity' => $qty,
                'subtotal' => $subtotal,
            ];
        }
    }

    return ['items' => $items, 'total' => $total];
}

function getCartCount() {
    $cart = getCart();
    return array_sum($cart);
}
