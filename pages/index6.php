<?php
require_once "../components/header.php";
require_once "../data/products.php";

$products = cadenceProductsForPage('index6.php');
?>
<main>
    <section class="framesets catalog-grid catalog-page">
        <?php foreach ($products as $product): ?>
            <a href="product.php?slug=<?php echo urlencode($product['slug']); ?>" class="frameset-item catalog-card">
                <div class="catalog-card-media">
                    <img class="catalog-card-image" src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="frameset-item-info catalog-card-info">
                    <p class="frameset-title"><?php echo htmlspecialchars($product['name']); ?></p>
                    <p class="frameset-price"><?php echo number_format((int) $product['price'], 0, ',', ' '); ?> ₽</p>
                </div>
            </a>
        <?php endforeach; ?>
    </section>
</main>
<?php require_once "../components/footer.php"; ?>