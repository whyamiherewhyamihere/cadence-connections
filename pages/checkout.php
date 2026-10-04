<?php
session_start();
require_once "../components/header.php";

// Подключение к PostgreSQL
$host = "pg";
$dbname = "studs";
$user = "s373760";
$pass = "Lk8pxd6QYt40Cabc";

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $last_name = $_POST['last_name'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $cartItems = $_SESSION['cart'] ?? [];

    if (empty($cartItems)) {
        die("Корзина пуста, заказ невозможен.");
    }

    // Проверка на пустые поля
    if (empty($last_name) || empty($first_name) || empty($phone_number)) {
        die("Пожалуйста, заполните все поля.");
    }

    // Подсчёт общей суммы и количества
    $totalItems = 0;
    $totalPrice = 0;
    foreach ($cartItems as $item) {
        if (is_numeric($item['price'])) {
            $price_numeric = (int) $item['price'];
        } else {
            $price_string = str_replace([' ₽', ' '], '', (string) $item['price']);
            $price_numeric = (int) $price_string;
        }
        $quantity = isset($item['quantity']) ? max(1, (int) $item['quantity']) : 1;
        $totalItems += $quantity;
        $totalPrice += $price_numeric * $quantity;
    }

    try {
        $pdo->beginTransaction();

        // 1. Сохраняем клиента и получаем его id
        $stmt = $pdo->prepare(
            "INSERT INTO customers (last_name, first_name, phone_number) 
             VALUES (:last_name, :first_name, :phone_number) RETURNING id"
        );
        $stmt->execute([
            ':last_name' => $last_name,
            ':first_name' => $first_name,
            ':phone_number' => $phone_number
        ]);
        $customerId = $stmt->fetchColumn();

        // 2. Создаём заказ и получаем его id
        $stmt = $pdo->prepare(
            "INSERT INTO orders (customer_id, total_items, total_price) 
             VALUES (:customer_id, :total_items, :total_price) RETURNING id"
        );
        $stmt->execute([
            ':customer_id' => $customerId,
            ':total_items' => $totalItems,
            ':total_price' => $totalPrice
        ]);
        $orderId = $stmt->fetchColumn();

        // 3. Сохраняем товары заказа
        $stmt = $pdo->prepare(
            "INSERT INTO order_items (order_id, product_name, product_price, quantity, product_option) 
             VALUES (:order_id, :product_name, :product_price, :quantity, :product_option)"
        );

        foreach ($cartItems as $item) {
            if (is_numeric($item['price'])) {
                $price_numeric = (int) $item['price'];
            } else {
                $price_string = str_replace([' ₽', ' '], '', (string) $item['price']);
                $price_numeric = (int) $price_string;
            }
            $quantity = isset($item['quantity']) ? max(1, (int) $item['quantity']) : 1;
            $productOption = isset($item['product_option']) ? (string) $item['product_option'] : '';

            $stmt->execute([
                ':order_id' => $orderId,
                ':product_name' => $item['name'],
                ':product_price' => $price_numeric,
                ':quantity' => $quantity,
                ':product_option' => $productOption
            ]);
        }

        $pdo->commit();

        // Очищаем корзину и сохраняем id заказа
        $_SESSION['last_order_id'] = $orderId;
        unset($_SESSION['cart']);

            // Страница подтверждения заказа (только UI)
            echo "<main class='order-success-main'>";
            echo "<section class='order-success'>";
            echo "<h2>ЗАКАЗ УСПЕШНО ОФОРМЛЕН</h2>";
            echo "<p class='order-success-text'>Спасибо! Мы свяжемся с вами в ближайшее время.</p>";
            echo "<a class='order-success-link' href='index2.php'>ВЕРНУТЬСЯ К ТОВАРАМ</a>";
            echo "</section>";
            echo "</main>";

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Ошибка при оформлении заказа: " . $e->getMessage());
    }
} else {
    http_response_code(405);
    echo "Метод не разрешен.";
}

require_once "../components/footer.php";
