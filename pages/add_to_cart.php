<?php
session_start();
require_once "../data/products.php";
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$slug = isset($_POST['slug']) ? trim((string) $_POST['slug']) : '';
$quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
$productOption = isset($_POST['product_option']) ? trim((string) $_POST['product_option']) : '';

if ($productOption === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Product option is required'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$product = $slug !== '' ? cadenceProductBySlug($slug) : null;
if ($product === null) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product slug'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$itemFound = false;
foreach ($_SESSION['cart'] as &$item) {
    if (($item['slug'] ?? '') === $slug && (($item['product_option'] ?? '') === $productOption)) {
        $item['quantity'] += $quantity;
        $itemFound = true;
        break;
    }
}
unset($item);

if (!$itemFound) {
    $item = [
        'slug' => $product['slug'],
        'name' => $product['name'],
        'price' => (int) $product['price'],
        'image' => $product['image'],
        'product_option' => $productOption,
        'quantity' => $quantity
    ];
    $_SESSION['cart'][] = $item;
}

$cartCount = 0;
foreach ($_SESSION['cart'] as $cartItem) {
    $cartCount += max(0, (int) ($cartItem['quantity'] ?? 0));
}

echo json_encode([
    'success' => true,
    'cart_count' => $cartCount,
    'positions_count' => count($_SESSION['cart'])
], JSON_UNESCAPED_UNICODE);
?>
