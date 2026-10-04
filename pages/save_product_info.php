<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $image = isset($_POST['image']) ? $_POST['image'] : '';
    $url = isset($_POST['url']) ? $_POST['url'] : '';
    
    $product_info = [
        'name' => $name,
        'price' => $price,
        'image' => $image,
        'url' => $url
    ];

    $_SESSION['last_viewed_product'] = $product_info;
} else {
    http_response_code(405);
}
?>