<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>
<footer>
        <div class="footer-container">
            <div class="footer-column footer-column-desktop">
                <h3>ИНФОРМАЦИЯ</h3>
                <ul>
                    <li><a <?php if($current_page == 'about.php') echo 'class = "current_page"' ?> href="about.php">О нас</a></li>
                    <li><a <?php if($current_page == 'contacts.php') echo 'class = "current_page"' ?> href="contacts.php">Контакты</a></li>
                    <li><a <?php if($current_page == 'faq.php') echo 'class = "current_page"' ?> href="faq.php">FAQ</a></li>
                </ul>
            </div>

            <div class="footer-column footer-column-desktop">
                <h3>МАГАЗИН</h3>
                <ul>
                    <li><a <?php if($current_page == 'index2.php') echo 'class = "current_page"' ?> href="index2.php">Фреймсеты</a></li>
                    <li><a <?php if($current_page == 'index3.php') echo 'class = "current_page"' ?> href="index3.php">Компоненты</a></li>
                    <li><a <?php if($current_page == 'index4.php') echo 'class = "current_page"' ?> href="index4.php">Колеса</a></li>
                </ul>
            </div>

            <div class="footer-column footer-column-desktop">
                <ul>
                    <li><a <?php if($current_page == 'index5.php') echo 'class = "current_page"' ?> href="index5.php">Аксессуары</a></li>
                    <li><a <?php if($current_page == 'index7.php') echo 'class = "current_page"' ?> href="index7.php">Одежда</a></li>
                </ul>
            </div>

            <div class="footer-column footer-accordion">
                <button type="button" class="footer-accordion-toggle" aria-expanded="false">
                    <span>ИНФОРМАЦИЯ</span>
                    <span class="footer-accordion-icon" aria-hidden="true">+</span>
                </button>
                <ul class="footer-accordion-panel">
                    <li><a <?php if($current_page == 'about.php') echo 'class = "current_page"' ?> href="about.php">О нас</a></li>
                    <li><a <?php if($current_page == 'contacts.php') echo 'class = "current_page"' ?> href="contacts.php">Контакты</a></li>
                    <li><a <?php if($current_page == 'faq.php') echo 'class = "current_page"' ?> href="faq.php">FAQ</a></li>
                </ul>
            </div>

            <div class="footer-column footer-accordion">
                <button type="button" class="footer-accordion-toggle" aria-expanded="false">
                    <span>МАГАЗИН</span>
                    <span class="footer-accordion-icon" aria-hidden="true">+</span>
                </button>
                <ul class="footer-accordion-panel">
                    <li><a <?php if($current_page == 'index2.php') echo 'class = "current_page"' ?> href="index2.php">Фреймсеты</a></li>
                    <li><a <?php if($current_page == 'index3.php') echo 'class = "current_page"' ?> href="index3.php">Компоненты</a></li>
                    <li><a <?php if($current_page == 'index4.php') echo 'class = "current_page"' ?> href="index4.php">Колеса</a></li>
                    <li><a <?php if($current_page == 'index5.php') echo 'class = "current_page"' ?> href="index5.php">Аксессуары</a></li>
                    <li><a <?php if($current_page == 'index7.php') echo 'class = "current_page"' ?> href="index7.php">Одежда</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-meta">
            <div class="footer-copyright">© 2026 cadence connections.</div>
            <p class="footer-slogan">designed for riders</p>
            </div>
    </footer>
    <script>
        (() => {
            const toggles = document.querySelectorAll('.footer-accordion-toggle');

            toggles.forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                    const panel = toggle.nextElementSibling;
                    const icon = toggle.querySelector('.footer-accordion-icon');

                    toggle.setAttribute('aria-expanded', String(!isOpen));
                    panel.classList.toggle('is-open', !isOpen);
                    icon.textContent = isOpen ? '+' : '−';
                });
            });
        })();
    </script>
</body>
</html> 
