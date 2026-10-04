<?php
require_once "../components/header.php";
require_once "../data/products.php";

$products = cadenceProductsForPage('index4.php');

function wheelsSubcategoryBySlug(string $slug): string {
    if (str_starts_with($slug, 'hub-')) {
        return 'hubs';
    }
    return 'wheels';
}
?>
<main class="components-main">
    <section class="components-page">
        <div class="components-breadcrumb-row">
            <p class="components-breadcrumb" id="wheels-breadcrumb">МАГАЗИН — КОЛЕСА</p>
        </div>

        <h1 class="components-title">Колеса</h1>

        <div class="components-controls-row">
            <div class="components-filters" role="tablist" aria-label="Подкатегории колес">
                <button class="components-filter-btn is-active" type="button" data-subcategory="all" data-label="ВСЕ">Все</button>
                <button class="components-filter-btn" type="button" data-subcategory="hubs" data-label="ВТУЛКИ">Втулки</button>
                <button class="components-filter-btn" type="button" data-subcategory="wheels" data-label="КОЛЕСА">Колеса</button>
            </div>

            <div class="components-meta">
                <span id="wheels-count">0 товаров</span>
                <span class="components-meta-dot">•</span>
                <button class="components-sort-btn" id="wheels-sort-btn" type="button">Сначала новые</button>
            </div>
        </div>
    </section>

    <section class="framesets catalog-grid catalog-page components-grid" id="wheels-grid">
        <?php $order = 1; ?>
        <?php foreach ($products as $product): ?>
            <?php
            $subcategory = wheelsSubcategoryBySlug((string) $product['slug']);
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
        const grid = document.getElementById('wheels-grid');
        const filterButtons = Array.from(document.querySelectorAll('.components-filter-btn'));
        const cards = Array.from(grid.querySelectorAll('.catalog-card'));
        const breadcrumb = document.getElementById('wheels-breadcrumb');
        const countEl = document.getElementById('wheels-count');
        const sortBtn = document.getElementById('wheels-sort-btn');

        let activeSubcategory = 'all';
        let sortMode = 'new';

        function applyWheelsView() {
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
                ? 'МАГАЗИН — КОЛЕСА'
                : `МАГАЗИН — КОЛЕСА — ${activeLabel}`;
            countEl.textContent = `${visibleCount} товаров`;
            sortBtn.textContent = sortMode === 'new' ? 'Сначала новые' : 'Сначала старые';
        }

        filterButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                filterButtons.forEach((item) => item.classList.remove('is-active'));
                btn.classList.add('is-active');
                activeSubcategory = btn.dataset.subcategory || 'all';
                applyWheelsView();
            });
        });

        sortBtn.addEventListener('click', () => {
            sortMode = sortMode === 'new' ? 'old' : 'new';
            applyWheelsView();
        });

        applyWheelsView();
    })();
</script>
<?php require_once "../components/footer.php"; ?>
