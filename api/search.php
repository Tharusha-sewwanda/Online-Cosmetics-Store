<?php
/**
 * AJAX endpoint used by assets/js/main.js to power live search & filtering.
 * Returns JSON: { products: [...] }
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/functions.php';

$filters = [
    'search'    => isset($_GET['search']) ? trim($_GET['search']) : '',
    'category'  => isset($_GET['category']) ? trim($_GET['category']) : '',
    'skin_type' => isset($_GET['skin_type']) ? trim($_GET['skin_type']) : '',
];

try {
    $products = getProducts($filters);
    echo json_encode(['products' => $products]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch products']);
}
