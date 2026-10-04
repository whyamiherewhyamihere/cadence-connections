<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$shop_pages = ['index2.php', 'index3.php', 'index4.php', 'index5.php', 'index7.php'];
$info_pages = ['about.php', 'contacts.php', 'faq.php'];
$is_shop_page = in_array($current_page, $shop_pages, true);
$is_info_page = in_array($current_page, $info_pages, true);

$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cartItem) {
        $cartCount += max(0, (int) ($cartItem['quantity'] ?? 0));
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="О нас — Cadence Connections, доверенный импортер и ритейлер велосипедных рам, колес, компонентов и аксессуаров для трековых велосипедистов.">
    <title>О нас</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bakbak+One&family=Inter:wght@400;600&family=Montserrat:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <header class="site-header">
        <div class="header-container">
            <a href="index1.php" class="logo-link" aria-label="Перейти на главную страницу">
                <h1 class="headTitle">cadence connections.</h1>
            </a>

            <nav class="header-nav" aria-label="Основная навигация">
                <ul class="top-menu" id="nav-menu">
                    <li class="top-menu-item dropdown">
                        <a class="top-menu-link <?php if ($is_shop_page)
                            echo 'current_page'; ?>" href="index2.php">МАГАЗИН</a>
                        <ul class="dropdown-menu">
                            <li><a <?php if ($current_page == 'index3.php')
                                echo 'class="current_page"'; ?> href="index3.php">Компоненты</a></li>
                            <li><a <?php if ($current_page == 'index2.php')
                                echo 'class="current_page"'; ?> href="index2.php">Фреймсеты</a></li>
                            <li><a <?php if ($current_page == 'index4.php')
                                echo 'class="current_page"'; ?> href="index4.php">Колеса</a></li>
                            <li><a <?php if ($current_page == 'index5.php')
                                echo 'class="current_page"'; ?> href="index5.php">Аксессуары</a></li>
                            <li><a <?php if ($current_page == 'index7.php')
                                echo 'class="current_page"'; ?> href="index7.php">Одежда</a></li>
                        </ul>
                    </li>

                    <li class="top-menu-item">
                        <a class="top-menu-link <?php if ($current_page == 'index8.php')
                            echo 'current_page'; ?>" href="index8.php">НОВОСТИ</a>
                    </li>

                    <li class="top-menu-item dropdown">
                        <a class="top-menu-link <?php if ($is_info_page)
                            echo 'current_page'; ?>" href="about.php">ИНФОРМАЦИЯ</a>
                        <ul class="dropdown-menu">
                            <li><a <?php if ($current_page == 'about.php')
                                echo 'class="current_page"'; ?> href="about.php">О нас</a></li>
                            <li><a <?php if ($current_page == 'contacts.php')
                                echo 'class="current_page"'; ?> href="contacts.php">Контакты</a></li>
                            <li><a <?php if ($current_page == 'faq.php')
                                echo 'class="current_page"'; ?> href="faq.php">FAQ</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="cart.php" class="cart-link" aria-label="Открыть корзину">
                    <img src="../images/finance_shopping-cart-grocery-store-b-s.svg" alt="Shopping Cart" class="cart-icon">
                    <span id="cart-count" class="cart-counter<?php echo $cartCount > 0 ? '' : ' is-empty'; ?>"><?php echo $cartCount; ?></span>
                </a>
                <button type="button" id="burger-menu" class="burger" aria-label="Открыть меню" aria-expanded="false"
                    aria-controls="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        <div class="mobile-menu-overlay" id="mobile-menu-overlay" hidden></div>
        <aside class="mobile-menu" id="mobile-menu" aria-hidden="true">
            <div class="mobile-menu-head">
                <a href="index1.php" class="mobile-menu-logo">CADENCE CONNECTIONS</a>
                <button type="button" class="mobile-menu-close" aria-label="Закрыть меню">×</button>
            </div>

            <nav class="mobile-menu-nav" aria-label="Мобильная навигация">
                <ul class="mobile-menu-primary">
                    <li><a href="index2.php">ФРЕЙМСЕТЫ</a></li>
                    <li><a href="index3.php">КОМПОНЕНТЫ</a></li>
                    <li><a href="index4.php">КОЛЕСА</a></li>
                    <li><a href="index5.php">АКСЕССУАРЫ</a></li>
                    <li><a href="index7.php">ОДЕЖДА</a></li>
                    <li><a href="index8.php">НОВОСТИ</a></li>
                </ul>

                <ul class="mobile-menu-secondary">
                    <li><a href="about.php">О НАС</a></li>
                    <li><a href="contacts.php">КОНТАКТЫ</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </nav>
        </aside>

        </header>

        <script>
            (() => {
                const burger = document.getElementById('burger-menu');
                const navMenu = document.getElementById('nav-menu');
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileOverlay = document.getElementById('mobile-menu-overlay');
                const mobileClose = document.querySelector('.mobile-menu-close');
                const mobileLinks = document.querySelectorAll('.mobile-menu a');
                const mobileQuery = window.matchMedia('(max-width: 768px)');

                const setMobileMenu = (isOpen) => {
                    mobileMenu.classList.toggle('is-open', isOpen);
                    mobileMenu.setAttribute('aria-hidden', String(!isOpen));
                    mobileOverlay.hidden = !isOpen;
                    document.body.classList.toggle('mobile-menu-open', isOpen);
                    burger.classList.toggle('active', isOpen);
                    burger.setAttribute('aria-expanded', String(isOpen));
                    burger.setAttribute('aria-label', isOpen ? 'Закрыть меню' : 'Открыть меню');
                };

                const closeMobileMenu = () => setMobileMenu(false);

                burger.addEventListener('click', () => {
                    if (mobileQuery.matches) {
                        setMobileMenu(!mobileMenu.classList.contains('is-open'));
                        return;
                    }

                    navMenu.classList.toggle('menu-open');
                    burger.classList.toggle('active');
                    burger.setAttribute('aria-expanded', String(burger.classList.contains('active')));
                });

                mobileClose.addEventListener('click', closeMobileMenu);
                mobileOverlay.addEventListener('click', closeMobileMenu);
                mobileLinks.forEach((link) => link.addEventListener('click', closeMobileMenu));

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeMobileMenu();
                    }
                });

                mobileQuery.addEventListener('change', () => {
                    closeMobileMenu();
                    navMenu.classList.remove('menu-open');
                    burger.classList.remove('active');
                    burger.setAttribute('aria-expanded', 'false');
                    burger.setAttribute('aria-label', 'Открыть меню');
                });
            })();
        </script>
