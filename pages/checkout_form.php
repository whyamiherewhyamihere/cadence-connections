<?php

// Открываем PHP-сессию: в ней хранятся корзина и токены оформления.
session_start();

// Подключаем каталог товаров и функцию поиска товара по slug.
require_once __DIR__ . '/../data/products.php';

// Получаем корзину посетителя; если ее нет, используем пустой массив.
$cartItems = $_SESSION['cart'] ?? [];

// Если корзина пустая, отправляем посетителя на страницу корзины.
if (!$cartItems) {
    // Отправляем заголовок перенаправления до вывода HTML.
    header('Location: cart.php');

    // Завершаем выполнение, чтобы форма пустого заказа не отображалась.
    exit;
}

// Счетчик общего количества единиц товаров.
$totalItems = 0;

// Общая стоимость корзины в рублях.
$totalPrice = 0;

// Последовательно обрабатываем каждую позицию корзины.
foreach ($cartItems as $item) {
    // Находим товар в серверном каталоге по его идентификатору slug.
    $product = cadenceProductBySlug($item['slug'] ?? '');

    // Проверяем, что количество является целым числом.
    // При некорректном значении filter_var() вернет false.
    $qty = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);

    // Товар должен существовать, а количество — находиться от 1 до 99.
    if (!$product || $qty === false || $qty < 1 || $qty > 99) {
        // Прерываем оформление некорректной корзины.
        exit('Проверьте товары в корзине.');
    }

    // Добавляем количество текущей позиции к общему количеству.
    $totalItems += $qty;

    // Берем цену из каталога и умножаем ее на количество.
    // Сохраненная в корзине цена здесь не используется.
    $totalPrice += (int) $product['price'] * $qty;
}

// Создаем CSRF-токен, если в текущей сессии он еще отсутствует.
// random_bytes(32) создает случайные байты, bin2hex() превращает их в строку.
// Этот токен обработчик checkout.php сравнит с токеном отправленной формы.
$_SESSION['checkout_csrf'] ??= bin2hex(random_bytes(32));

// Создаем массив токенов отдельных форм оформления, если его еще нет.
$_SESSION['checkout_tokens'] ??= [];

// Проверяем возраст ранее созданных токенов оформления.
foreach ($_SESSION['checkout_tokens'] as $key => $issuedAt) {
    // 7200 секунд — два часа: удаляем токены старше этого времени.
    if ($issuedAt < time() - 7200) {
        unset($_SESSION['checkout_tokens'][$key]);
    }
}

// Оставляем последние 19 токенов перед добавлением нового.
// После добавления в массиве будет не более 20 токенов.
$_SESSION['checkout_tokens'] = array_slice(
    $_SESSION['checkout_tokens'], // Массив сохраненных токенов.
    -19,                         // Берем последние 19 элементов.
    null,                        // Берем элементы до конца массива.
    true                         // Сохраняем строковые ключи токенов.
);

// Создаем отдельный случайный токен для этой открытой формы.
// Повторная отправка формы с этим токеном будет использовать тот же заказ.
$checkoutToken = bin2hex(random_bytes(32));

// Сохраняем токен в сессии вместе со временем его создания.
$_SESSION['checkout_tokens'][$checkoutToken] = time();

// Подключаем общую шапку сайта после подготовки данных и перенаправлений.
require_once __DIR__ . '/../components/header.php';

?>

<!-- Основное содержимое страницы оформления заказа. -->
<main class="checkout-main">
    <div id="checkout" class="checkout-page">

        <!-- Отправляем данные покупателя и токены в checkout.php методом POST. -->
        <form action="checkout.php" method="post" class="checkout-layout">

            <!-- CSRF-токен связывает отправленный запрос с сессией посетителя. -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?= $_SESSION['checkout_csrf'] ?>"
            >

            <!-- Токен конкретной формы используется для защиты от повторного создания заказа. -->
            <input
                type="hidden"
                name="checkout_token"
                value="<?= $checkoutToken ?>"
            >

            <!-- Левая часть формы: навигация и сведения о покупателе. -->
            <div class="checkout-left">
                <p class="checkout-breadcrumb">
                    МАГАЗИН — ОФОРМЛЕНИЕ ЗАКАЗА
                </p>

                <!-- Возврат в корзину для изменения товаров или количества. -->
                <a class="checkout-back" href="cart.php">
                    <span>&#8592;</span> Вернуться в корзину
                </a>

                <h2>ОФОРМЛЕНИЕ ЗАКАЗА</h2>

                <!-- Фамилия отправляется в обработчик под именем last_name. -->
                <!-- required требует заполнения в браузере; сервер проверяет поле отдельно. -->
                <label class="checkout-field">
                    <span>Фамилия</span>
                    <input
                        type="text"
                        name="last_name"
                        placeholder="Фамилия"
                        autocomplete="family-name"
                        required
                    >
                </label>

                <!-- Имя отправляется под именем first_name. -->
                <!-- autocomplete помогает браузеру подставить сохраненное имя. -->
                <label class="checkout-field">
                    <span>Имя</span>
                    <input
                        type="text"
                        name="first_name"
                        placeholder="Имя"
                        autocomplete="given-name"
                        required
                    >
                </label>

                <!-- Телефон отправляется под именем phone_number. -->
                <!-- type="tel" не проверяет формат номера; placeholder показывает пример. -->
                <label class="checkout-field">
                    <span>Телефон</span>
                    <input
                        type="tel"
                        name="phone_number"
                        placeholder="+7 (___) ___-__-__"
                        autocomplete="tel"
                        required
                    >
                </label>
            </div>

            <!-- Правая часть формы: рассчитанная стоимость и кнопка отправки. -->
            <div class="checkout-right">
                <h2>ИТОГ</h2>

                <!-- Итоговые значения рассчитаны PHP по серверному каталогу. -->
                <table class="checkout-total-table">
                    <tbody>

                        <!-- Показываем количество единиц товаров и их стоимость. -->
                        <tr>
                            <td>
                                <p>
                                    Товары
                                    <span>(<?php echo $totalItems; ?>)</span>
                                </p>
                            </td>

                            <!-- Форматируем сумму: без копеек, с пробелами между разрядами. -->
                            <td>
                                <?php echo number_format($totalPrice, 0, ',', ' '); ?> ₽
                            </td>
                        </tr>

                        <!-- Расчет скидок пока отсутствует: отображается фиксированный ноль. -->
                        <tr>
                            <td>Скидка</td>
                            <td>0 ₽</td>
                        </tr>

                        <!-- Итог равен стоимости товаров, поскольку скидки не применяются. -->
                        <tr>
                            <td>
                                <strong>ИТОГ:</strong>
                            </td>
                            <td>
                                <strong>
                                    <?php echo number_format($totalPrice, 0, ',', ' '); ?> ₽
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Отправляем форму; заказ и платеж создает обработчик checkout.php. -->
                <button class="checkout-submit-btn" type="submit">
                    ПЕРЕЙТИ К ОПЛАТЕ
                </button>
            </div>
        </form>
    </div>
</main>

<?php
// Подключаем общий подвал сайта.
require_once "../components/footer.php";
?>