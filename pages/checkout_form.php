<?php
session_start();
require_once "../components/header.php";

// Берём корзину из сессии
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$totalItems = 0;
$totalPrice = 0;

foreach ($cartItems as $item) {
    if (is_numeric($item['price'])) {
        $price_numeric = (int) $item['price'];
    } else {
        $price_string = str_replace([' ₽', ' '], '', (string) $item['price']);
        $price_numeric = (int) $price_string;
    }
    $totalItems += $item['quantity'];
    $totalPrice += $price_numeric * $item['quantity'];
}
?>

<main class="checkout-main">
<div id="checkout" class="checkout-page">
    <form action="checkout.php" method="post" class="checkout-layout">
        <div class="checkout-left">
            <p class="checkout-breadcrumb">МАГАЗИН — ОФОРМЛЕНИЕ ЗАКАЗА</p>
            <a class="checkout-back" href="cart.php"><span>&#8592;</span> Вернуться в корзину</a>
            <h2>ОФОРМЛЕНИЕ ЗАКАЗА</h2>

            <label class="checkout-field">
                <span>Фамилия</span>
                <input type="text" name="last_name" placeholder="Фамилия" autocomplete="family-name" required>
            </label>

            <label class="checkout-field">
                <span>Имя</span>
                <input type="text" name="first_name" placeholder="Имя" autocomplete="given-name" required>
            </label>

            <label class="checkout-field">
                <span>Телефон</span>
                <input type="tel" name="phone_number" placeholder="+7 (___) ___-__-__" autocomplete="tel" required>
            </label>
        </div>

        <div class="checkout-right">
            <h2>ИТОГ</h2>
            <table class="checkout-total-table">
                <tbody>
                    <tr>
                        <td><p>Товары <span>(<?php echo $totalItems; ?>)</span></p></td>
                        <td><?php echo number_format($totalPrice, 0, ',', ' '); ?> ₽</td>
                    </tr>
                    <tr>
                        <td>Скидка</td>
                        <td>0 ₽</td>
                    </tr>
                    <tr>
                        <td><strong>ИТОГ:</strong></td>
                        <td><strong><?php echo number_format($totalPrice, 0, ',', ' '); ?> ₽</strong></td>
                    </tr>
                </tbody>
            </table>

            <button class="checkout-submit-btn" type="submit">
                ОТПРАВИТЬ
            </button>
        </div>
    </form>
</div>
</main>

<?php require_once "../components/footer.php"; ?>
