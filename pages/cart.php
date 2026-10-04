<?php
session_start();
require_once "../components/header.php";

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


function cartCategoryLabel(array $item): string {
    $slug = isset($item['slug']) ? (string) $item['slug'] : '';
    if (str_starts_with($slug, 'frameset-')) {
        return 'Фреймсеты';
    }
    if (str_starts_with($slug, 'component-')) {
        return 'Компоненты';
    }
    if (str_starts_with($slug, 'wheel-')) {
        return 'Колеса';
    }
    if (str_starts_with($slug, 'accessory-')) {
        return 'Аксессуары';
    }
    if (str_starts_with($slug, 'clothes-')) {
        return 'Одежда';
    }
    return 'Товар';
}
?>

<main class="cart-main">
<div id="cart" class="cart-container cart-page">
    <div class="cart-left">
        <p class="cart-breadcrumb">МАГАЗИН — КОРЗИНА</p>
        <a class="cart-back" href="index2.php"><span>&#8592;</span> Назад в каталог</a>
        <h2><?php echo empty($cartItems) ? 'КОРЗИНА ПУСТА' : 'КОРЗИНА'; ?></h2>
        <?php if (!empty($cartItems)): ?>
            <table class="cart-items-table">
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td class="cart-item-image"><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="product image"></td>
                            <td class="cart-item-info">
                                <p class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="cart-item-category"><?php echo htmlspecialchars(cartCategoryLabel($item)); ?></p>
                                <p class="cart-item-qty">Количество: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            </td>
                            <td class="cart-item-price">
                                <?php
                                $itemPrice = is_numeric($item['price'])
                                    ? (int) $item['price']
                                    : (int) str_replace([' ₽', ' '], '', (string) $item['price']);
                                ?>
                                <?php echo number_format($itemPrice, 0, ',', ' '); ?> ₽
                            </td>
                            <td class="cart-item-actions">
                                <button class="delete-item-btn" data-slug="<?php echo htmlspecialchars($item['slug'] ?? ''); ?>" aria-label="Удалить товар из корзины">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"></path>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div class="cart-right">
        <h2>ИТОГ</h2>
        <div class="cart-summary-body">
            <table class="cart-total-table">
                <tbody>
                    <tr>
                        <td><p>Товары <span>(<?php echo $totalItems; ?>)</span></p></td>
                        <td><?php echo number_format($totalPrice, 0, ',', ' '); ?> ₽</td>
                    </tr>
                    <tr>
                        <td>Скидка</td>
                        <td>0 ₽</td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td>ИТОГ:</td>
                        <td><?php echo number_format($totalPrice, 0, ',', ' '); ?> ₽</td>
                    </tr>
                </tbody>
            </table>
            
            <button class="cart-checkout-btn" onclick="window.location.href='checkout_form.php'">ПЕРЕЙТИ К ОФОРМЛЕНИЮ</button>
        </div>
    </div>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-item-btn').forEach(button => {
        button.addEventListener('click', event => {
            const clickedButton = event.target.closest('.delete-item-btn');
            if (clickedButton) {
                const productSlug = clickedButton.dataset.slug;
            
                fetch('delete_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `slug=${encodeURIComponent(productSlug)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    }
                })
            }
        });
    });
});
</script>

<?php require_once "../components/footer.php" ?>
