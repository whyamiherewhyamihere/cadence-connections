<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slugToDelete = isset($_POST['slug']) ? trim((string) $_POST['slug']) : '';

    if ($slugToDelete !== '' && isset($_SESSION['cart'])) {
        $cart_new = [];
        $deleted = false;
        foreach ($_SESSION['cart'] as $item) {
            if (($item['slug'] ?? '') !== $slugToDelete) {
                $cart_new[] = $item;
            } else {
                $deleted = true;
            }
        }
        $_SESSION['cart'] = $cart_new;
        if ($deleted) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'not-found']);
        }
    } else {
        echo json_encode(['status' => 'invalid-request']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'method-not-allowed']);
}
?>