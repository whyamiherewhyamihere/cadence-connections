<?php
require_once "../components/header.php";
require_once "../data/products.php";

$products = cadenceProductsForPage('index7.php');

function clothesSubcategoryBySlug(string $slug): string {
    if (str_contains($slug, 'tshirt')) {
        return 'tshirts';
    }
    if (str_contains($slug, 'gloves')) {
        return 'gloves';
    }
    if (str_contains($slug, 'cap') || str_contains($slug, 'beanie')) {
        return 'headwear';
    }
    if (str_contains($slug, 'shirt')) {
        return 'shirts';
    }
    return 'tshirts';
}
?>
<main class="components-main">
    <section class="components-page">
        <div class="components-breadcrumb-row">
            <p class="components-breadcrumb" id="clothes-breadcrumb">МАГАЗИН — ОДЕЖДА</p>
        </div>

        <h1 class="components-title">Одежда</h1>

        <div class="components-controls-row">
            <div class="components-filters" role="tablist" aria-label="Подкатегории одежды">
                <button class="components-filter-btn is-active" type="button" data-subcategory="all" data-label="ВСЕ">Все</button>
                <button class="components-filter-btn" type="button" data-subcategory="tshirts" data-label="ФУТБОЛКИ">Футболки</button>
                <button class="components-filter-btn" type="button" data-subcategory="gloves" data-label="ПЕРЧАТКИ">Перчатки</button>
                <button class="components-filter-btn" type="button" data-subcategory="headwear" data-label="ГОЛОВНЫЕ УБОРЫ">Головные уборы</button>
                <button class="components-filter-btn" type="button" data-subcategory="shirts" data-label="РУБАШКИ">Рубашки</button>
            </div>

            <div class="components-meta">
                <span id="clothes-count">0 товаров</span>
                <span class="components-meta-dot">•</span>
                <button class="components-sort-btn" id="clothes-sort-btn" type="button">Сначала новые</button>
            </div>
        </div>
    </section>

    <section class="framesets catalog-grid catalog-page components-grid" id="clothes-grid">
        <?php $order = 1; ?>
        <?php foreach ($products as $product): ?>
            <?php
            $subcategory = clothesSubcategoryBySlug((string) $product['slug']);
            ?>
            <a href="product.php?slug=<?php echo urlencode($product['slug']); ?>" class="frameset-item catalog-card"
                data-subcategory="<?php echo htmlspecialchars($subcategory); ?>"
                data-order="<?php echo $order; ?>">
                <div class="catalog-card-media">
                    <img class="catalog-card-image" src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="frameset-item-info catalog-card-info">
                    <p class="frameset-title"><?php echo htmlspecialchars($product['name']); ?></p>
                    <p class="frameset-price"><?php echo number_format((int) $product['price'], 0, ',', ' '); ?> ₽</p>
                </div>
            </a>
            <?php $order++; ?>
        <?php endforeach; ?>
    </section>
</main>
<script>
    (() => {
        const grid = document.getElementById('clothes-grid');
        const filterButtons = Array.from(document.querySelectorAll('.components-filter-btn'));
        const cards = Array.from(grid.querySelectorAll('.catalog-card'));
        const breadcrumb = document.getElementById('clothes-breadcrumb');
        const countEl = document.getElementById('clothes-count');
        const sortBtn = document.getElementById('clothes-sort-btn');

        let activeSubcategory = 'all';
        let sortMode = 'new';

        function applyClothesView() {
            const sortedCards = [...cards].sort((a, b) => {
                const orderA = Number(a.dataset.order) || 0;
                const orderB = Number(b.dataset.order) || 0;
                return sortMode === 'new' ? orderB - orderA : orderA - orderB;
            });

            sortedCards.forEach((card) => {
                grid.appendChild(card);
                const matches = activeSubcategory === 'all' || card.dataset.subcategory === activeSubcategory;
                card.hidden = !matches;
            });

            const activeBtn = filterButtons.find((btn) => btn.classList.contains('is-active'));
            const activeLabel = activeBtn ? activeBtn.dataset.label : 'ВСЕ';
            const visibleCount = sortedCards.filter((card) => !card.hidden).length;

            breadcrumb.textContent = activeSubcategory === 'all'
                ? 'МАГАЗИН — ОДЕЖДА'
                : `МАГАЗИН — ОДЕЖДА — ${activeLabel}`;
            countEl.textContent = `${visibleCount} товаров`;
            sortBtn.textContent = sortMode === 'new' ? 'Сначала новые' : 'Сначала старые';
        }

        filterButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                filterButtons.forEach((item) => item.classList.remove('is-active'));
                btn.classList.add('is-active');
                activeSubcategory = btn.dataset.subcategory || 'all';
                applyClothesView();
            });
        });

        sortBtn.addEventListener('click', () => {
            sortMode = sortMode === 'new' ? 'old' : 'new';
            applyClothesView();
        });

        applyClothesView();
    })();
</script>
<?php require_once "../components/footer.php"; ?>
