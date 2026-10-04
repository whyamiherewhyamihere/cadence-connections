<?php
require_once "../components/header.php";
require_once "../data/products.php";

$products = cadenceProductsForPage('index5.php');

function accessoriesSubcategoryBySlug(string $slug): string {
    if (str_contains($slug, 'fender') || str_contains($slug, 'saver')) {
        return 'fenders';
    }
    if (str_contains($slug, 'sticker')) {
        return 'stickers';
    }
    if (str_contains($slug, 'rack')) {
        return 'racks';
    }
    if (str_contains($slug, 'bell') || str_contains($slug, 'bottle')) {
        return 'attachments';
    }
    return 'attachments';
}
?>
<main class="components-main">
    <section class="components-page">
        <div class="components-breadcrumb-row">
            <p class="components-breadcrumb" id="accessories-breadcrumb">МАГАЗИН — АКСЕССУАРЫ</p>
        </div>

        <h1 class="components-title">Аксессуары</h1>

        <div class="components-controls-row">
            <div class="components-filters" role="tablist" aria-label="Подкатегории аксессуаров">
                <button class="components-filter-btn is-active" type="button" data-subcategory="all" data-label="ВСЕ">Все</button>
                <button class="components-filter-btn" type="button" data-subcategory="fenders" data-label="КРЫЛЬЯ">Крылья</button>
                <button class="components-filter-btn" type="button" data-subcategory="stickers" data-label="СТИКЕРЫ">Стикеры</button>
                <button class="components-filter-btn" type="button" data-subcategory="racks" data-label="БАГАЖНИКИ">Багажники</button>
                <button class="components-filter-btn" type="button" data-subcategory="attachments" data-label="НАВЕСНОЕ">Навесное</button>
            </div>

            <div class="components-meta">
                <span id="accessories-count">0 товаров</span>
                <span class="components-meta-dot">•</span>
                <button class="components-sort-btn" id="accessories-sort-btn" type="button">Сначала новые</button>
            </div>
        </div>
    </section>

    <section class="framesets catalog-grid catalog-page components-grid" id="accessories-grid">
        <?php $order = 1; ?>
        <?php foreach ($products as $product): ?>
            <?php
            $subcategory = accessoriesSubcategoryBySlug((string) $product['slug']);
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
        const grid = document.getElementById('accessories-grid');
        const filterButtons = Array.from(document.querySelectorAll('.components-filter-btn'));
        const cards = Array.from(grid.querySelectorAll('.catalog-card'));
        const breadcrumb = document.getElementById('accessories-breadcrumb');
        const countEl = document.getElementById('accessories-count');
        const sortBtn = document.getElementById('accessories-sort-btn');

        let activeSubcategory = 'all';
        let sortMode = 'new';

        function applyAccessoriesView() {
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
                ? 'МАГАЗИН — АКСЕССУАРЫ'
                : `МАГАЗИН — АКСЕССУАРЫ — ${activeLabel}`;
            countEl.textContent = `${visibleCount} товаров`;
            sortBtn.textContent = sortMode === 'new' ? 'Сначала новые' : 'Сначала старые';
        }

        filterButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                filterButtons.forEach((item) => item.classList.remove('is-active'));
                btn.classList.add('is-active');
                activeSubcategory = btn.dataset.subcategory || 'all';
                applyAccessoriesView();
            });
        });

        sortBtn.addEventListener('click', () => {
            sortMode = sortMode === 'new' ? 'old' : 'new';
            applyAccessoriesView();
        });

        applyAccessoriesView();
    })();
</script>
<?php require_once "../components/footer.php"; ?>
