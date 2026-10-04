<?php
require_once "../components/header.php";
require_once "../data/products.php";

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$product = $slug !== '' ? cadenceProductBySlug($slug) : null;

if ($product === null) {
    http_response_code(404);
    ?>
    <main class="product-main">
    <section id="product" class="product-layout product-layout--empty">
        <div class="product-right product-right--empty">
            <h1 class="product-name">Товар не найден</h1>
            <p class="product-price">Проверьте ссылку на карточку товара.</p>
            <button class="product-action" onclick="window.location.href='index2.php'">ВЕРНУТЬСЯ В КАТАЛОГ</button>
        </div>
    </section>
    </main>
    <?php
    require_once "../components/footer.php";
    exit;
}

$previousPageUrl = $product['category_page'] ?? 'index2.php';
$productPrice = number_format((int) $product['price'], 0, ',', ' ') . ' ₽';
$productCategoryLabels = [
    'index2.php' => 'Фреймсеты',
    'index3.php' => 'Компоненты',
    'index4.php' => 'Колеса',
    'index5.php' => 'Аксессуары',
    'index7.php' => 'Одежда',
];
$productCategoryLabel = $productCategoryLabels[$previousPageUrl] ?? 'Каталог';

$productOptionMap = [
    'frameset-veloci' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-vendetta' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-skream' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-thomson' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-dolan' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-gan-well-pro' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-soma' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-soyo' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-ritchey' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-cinelli' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-tsunami' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'frameset-skream-12' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L', 'XL'],
    ],
    'fork-surly' => [
        'label' => 'Длина штока:',
        'options' => ['S', 'M', 'L'],
    ],
    'fork-a-frame' => [
        'label' => 'Длина штока:',
        'options' => ['S', 'M', 'L'],
    ],
    'fork-alpina' => [
        'label' => 'Длина штока:',
        'options' => ['S', 'M', 'L'],
    ],
    'fork-vendetta' => [
        'label' => 'Длина штока:',
        'options' => ['S', 'M', 'L'],
    ],
    'wheel-grancompe-tb14' => [
        'label' => 'Тип покрышки:',
        'options' => ['Клинчер', 'Трубка'],
    ],
    'wheel-mack-hed-belgium' => [
        'label' => 'Тип покрышки:',
        'options' => ['Клинчер', 'Трубка'],
    ],
    'wheel-hed' => [
        'label' => 'Тип покрышки:',
        'options' => ['Клинчер', 'Трубка'],
    ],
    'wheel-phil-velocity-pink' => [
        'label' => 'Тип покрышки:',
        'options' => ['Клинчер', 'Трубка'],
    ],
    'wheel-dura-ace-tb14' => [
        'label' => 'Тип покрышки:',
        'options' => ['Клинчер', 'Трубка'],
    ],
    'wheel-dt-swiss' => [
        'label' => 'Тип покрышки:',
        'options' => ['Клинчер', 'Трубка'],
    ],
    'hub-mack-silver' => [
        'label' => 'Резьба:',
        'options' => ['Одна резьба', 'Две резьбы'],
    ],
    'hub-mack-black' => [
        'label' => 'Резьба:',
        'options' => ['Одна резьба', 'Две резьбы'],
    ],
    'hub-phil-wood-red' => [
        'label' => 'Резьба:',
        'options' => ['Одна резьба', 'Две резьбы'],
    ],
    'hub-reina-black' => [
        'label' => 'Резьба:',
        'options' => ['Одна резьба', 'Две резьбы'],
    ],
    'wheel-carbon-blade' => [
        'label' => 'Тип:',
        'options' => ['Клинчер', 'Трубка'],
    ],
    'wheel-dt-swiss-8' => [
        'label' => 'Тип:',
        'options' => ['Клинчер', 'Трубка'],
    ],
    'accessory-mash-rack' => [
        'label' => 'Материал:',
        'options' => ['Титан', 'Сталь'],
    ],
    'accessory-knog-bell' => [
        'label' => 'Громкость:',
        'options' => ['Средняя', 'Высокая'],
    ],
    'accessory-sugino-sticker' => [
        'label' => 'Размер:',
        'options' => ['3 см', '5 см'],
    ],
    'accessory-ass-saver-fender' => [
        'label' => 'Длина:',
        'options' => ['40 см', '65 см'],
    ],
    'accessory-sim-works-bottle' => [
        'label' => 'Объем:',
        'options' => ['500 мл', '650 мл'],
    ],
    'accessory-a-frame-rack' => [
        'label' => 'Материал:',
        'options' => ['Титан', 'Сталь'],
    ],
    'accessory-ass-saver-short' => [
        'label' => 'Длина:',
        'options' => ['15 см', '20 см'],
    ],
    'accessory-cinelli-sticker' => [
        'label' => 'Размер:',
        'options' => ['2 см', '3 см'],
    ],
    'accessory-nitto-bottle-cage' => [
        'label' => 'Объем:',
        'options' => ['0.5', '0.7'],
    ],
    'clothes-kashimax-gloves' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L'],
    ],
    'clothes-cinelli-cap' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L'],
    ],
    'clothes-nitto-tshirt' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L'],
    ],
    'clothes-sim-works-tshirt' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L'],
    ],
    'clothes-phil-beanie' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L'],
    ],
    'clothes-araya-cap' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L'],
    ],
    'clothes-nitto-shirt' => [
        'label' => 'Размер:',
        'options' => ['S', 'M', 'L'],
    ],
    'component-sugino-cranks' => [
        'label' => 'Длина:',
        'options' => ['165 мм', '170 мм', '172.5 мм'],
    ],
    'component-vision-cranks' => [
        'label' => 'Длина:',
        'options' => ['165 мм', '170 мм', '172.5 мм'],
    ],
    'component-ritchey-seatpost-silver' => [
        'label' => 'Длина:',
        'options' => ['300 мм', '350 мм', '400 мм'],
    ],
    'component-ritchey-seatpost-black' => [
        'label' => 'Длина:',
        'options' => ['300 мм', '350 мм', '400 мм'],
    ],
    'component-kashimax-saddle' => [
        'label' => 'Ширина:',
        'options' => ['130 мм', '145 мм', '153 мм'],
    ],
    'component-selle-italia-saddle' => [
        'label' => 'Ширина:',
        'options' => ['130 мм', '145 мм', '153 мм'],
    ],
    'component-shimano-pedals' => [
        'label' => 'Резьба:',
        'options' => ['ITA', 'BSA'],
    ],
    'component-mks-pedals' => [
        'label' => 'Резьба:',
        'options' => ['ITA', 'BSA'],
    ],
    'component-sugino-chainring' => [
        'label' => 'Количество зубов:',
        'options' => ['43 зуба', '46 зубов', '47 зубов', '49 зубов', '50 зубов'],
    ],
    'component-aarn-chainring' => [
        'label' => 'Количество зубов:',
        'options' => ['43 зуба', '46 зубов', '47 зубов', '49 зубов', '50 зубов'],
    ],
    'component-nitto-handlebar' => [
        'label' => 'Толщина зажима:',
        'options' => ['25.4 мм', '31.8 мм'],
    ],
    'component-cinelli-handlebar' => [
        'label' => 'Ширина:',
        'options' => ['400 мм', '420 мм', '440 мм'],
    ],
    'component-nitto-stem' => [
        'label' => 'Длина:',
        'options' => ['100 мм', '110 мм', '120 мм', '130 мм'],
    ],
    'component-cinelli-stem' => [
        'label' => 'Длина:',
        'options' => ['100 мм', '120 мм', '140 мм'],
    ],
    'component-eai-cog-silver' => [
        'label' => 'Количество зубов:',
        'options' => ['14 зубов', '15 зубов', '16 зубов', '17 зубов', '18 зубов', '19 зубов'],
    ],
    'component-chris-king-headset' => [
        'label' => 'Размер:',
        'options' => ['1 inch', '1 1/8 inch'],
    ],
];
$productOptionConfig = $productOptionMap[$product['slug']] ?? [
    'label' => 'Опция товара:',
    'options' => ['Стандарт'],
];
$productOptionPlaceholderMap = [
    'Размер:' => 'Выберите размер',
    'Длина:' => 'Выберите длину',
    'Длина штока:' => 'Выберите длину штока',
    'Ширина:' => 'Выберите ширину',
    'Резьба:' => 'Выберите резьбу',
    'Тип:' => 'Выберите тип',
    'Количество зубов:' => 'Выберите количество зубов',
    'Толщина зажима:' => 'Выберите толщину зажима',
    'Тип покрышки:' => 'Выберите тип покрышки',
    'Материал:' => 'Выберите материал',
    'Громкость:' => 'Выберите громкость',
    'Объем:' => 'Выберите объем',
];
$productOptionPlaceholder = $productOptionPlaceholderMap[$productOptionConfig['label']] ?? 'Выберите опцию';
$isOversizedProduct = str_starts_with((string) $product['slug'], 'frameset-')
    || str_starts_with((string) $product['slug'], 'wheel-');
$deliveryTitle = $isOversizedProduct ? 'Груз нестандартных размеров' : 'Бесплатная, быстрая доставка';
$deliveryText = $isOversizedProduct
    ? 'Исключено из бесплатной доставки. Стоимость фиксированной доставки будет указана при оформлении заказа. Негабаритные товары возврату не подлежат.'
    : 'Заказы, которые стоят больше 10000 ₽, будут отправлены в любую точку России бесплатно.';

$productDetailsMap = [
    'frameset-veloci' => [
        'description' => 'Минималистичный стальной фреймсет для городских и трековых сборок. Подходит для ежедневного использования и фиксированных передач.',
        'specs' => [
            'Материал: steel',
            'Тип: track frameset',
            'Вилка: steel fork',
            'Размеры: S / M / L / XL',
            'Цвет: silver',
        ],
    ],
    'frameset-vendetta' => [
        'description' => 'Жесткий алюминиевый фреймсет, рассчитанный на агрессивное городское катание и высокую отзывчивость велосипеда.',
        'specs' => [
            'Материал: aluminum',
            'Тип: fixed gear / track',
            'Вилка: carbon fork',
            'Размеры: S / M / L / XL',
            'Цвет: black',
        ],
    ],
    'frameset-skream' => [
        'description' => 'Современный трековый фреймсет с аэродинамичной геометрией и высокой жесткостью конструкции.',
        'specs' => [
            'Материал: aluminum',
            'Геометрия: pursuit / track',
            'Вилка: carbon',
            'Размеры: S / M / L / XL',
            'Цвет: raw aluminum',
        ],
    ],
    'frameset-thomson' => [
        'description' => 'Легкий городской фреймсет с классической геометрией и универсальной посадкой для ежедневных поездок.',
        'specs' => [
            'Материал: steel',
            'Тип: urban track',
            'Вилка: steel fork',
            'Размеры: S / M / L / XL',
            'Цвет: gray',
        ],
    ],
    'frameset-dolan' => [
        'description' => 'Профессиональный трековый фреймсет, разработанный для высокой скорости и стабильности на велотреке.',
        'specs' => [
            'Материал: aluminum',
            'Назначение: velodrome / track racing',
            'Вилка: carbon fork',
            'Размеры: S / M / L / XL',
            'Цвет: black',
        ],
    ],
    'frameset-gan-well-pro' => [
        'description' => 'Классический японский трековый фреймсет с традиционной геометрией и высокой точностью изготовления.',
        'specs' => [
            'Материал: steel',
            'Производство: Japan',
            'Тип: NJS inspired',
            'Размеры: S / M / L / XL',
            'Цвет: chrome',
        ],
    ],
    'frameset-soma' => [
        'description' => 'Стальной фреймсет для комфортных городских и дальних поездок с возможностью использования фиксированной передачи.',
        'specs' => [
            'Материал: chromoly steel',
            'Тип: urban / track',
            'Вилка: steel fork',
            'Размеры: S / M / L / XL',
            'Цвет: dark gray',
        ],
    ],
    'frameset-soyo' => [
        'description' => 'Премиальный японский фреймсет ручной сборки, ориентированный на классические трековые велосипеды.',
        'specs' => [
            'Материал: steel',
            'Производство: Japan',
            'Тип: track frameset',
            'Размеры: S / M / L / XL',
            'Цвет: pearl white',
        ],
    ],
    'frameset-ritchey' => [
        'description' => 'Стальной фреймсет с классической геометрией Ritchey для городских fixed gear и трековых сборок.',
        'specs' => [
            'Материал: chromoly steel',
            'Геометрия: classic track',
            'Тип: fixed gear / track frameset',
            'Совместимость: 700c wheels',
            'Размеры: S / M / L / XL',
            'Вилка: steel fork',
            'Назначение: urban / track',
        ],
    ],
    'frameset-cinelli' => [
        'description' => 'Алюминиевый фреймсет Cinelli с отзывчивой посадкой и жесткой платформой для быстрых городских сборок.',
        'specs' => [
            'Материал: aluminum',
            'Геометрия: track / criterium',
            'Тип: fixed gear frameset',
            'Совместимость: 700c wheels',
            'Размеры: S / M / L / XL',
            'Вилка: carbon fork',
            'Назначение: urban performance',
        ],
    ],
    'frameset-tsunami' => [
        'description' => 'Современный алюминиевый фреймсет с агрессивной геометрией для фиксированной передачи и городского катания.',
        'specs' => [
            'Материал: aluminum',
            'Геометрия: aggressive track',
            'Тип: fixed gear / track',
            'Совместимость: 700c wheels',
            'Размеры: S / M / L / XL',
            'Вилка: carbon fork',
            'Назначение: street / track',
        ],
    ],
    'frameset-skream-12' => [
        'description' => 'Трековый фреймсет Skream с жесткой алюминиевой рамой и геометрией для скоростных городских сборок.',
        'specs' => [
            'Материал: aluminum',
            'Геометрия: aero track',
            'Тип: fixed gear frameset',
            'Совместимость: 700c wheels',
            'Размеры: S / M / L / XL',
            'Вилка: carbon fork',
            'Назначение: track / urban racing',
        ],
    ],
    'fork-surly' => [
        'description' => 'Стальная вилка Surly для надежных городских и fixed gear сборок с акцентом на прочность и стабильность.',
        'specs' => [
            'Материал: chromoly steel',
            'Тип: rigid fork',
            'Совместимость: 700c wheels',
            'Цвет: black',
            'Размеры: S / M / L',
            'Назначение: urban / fixed gear',
        ],
    ],
    'fork-a-frame' => [
        'description' => 'Легкая алюминиевая вилка A-Frame для трековых и городских рам с нейтральной управляемостью.',
        'specs' => [
            'Материал: aluminum',
            'Тип: rigid track fork',
            'Совместимость: 700c wheels',
            'Цвет: silver',
            'Размеры: S / M / L',
            'Назначение: track / urban',
        ],
    ],
    'fork-alpina' => [
        'description' => 'Карбоновая вилка Alpina для трековых велосипедов, рассчитанная на низкий вес и точную управляемость.',
        'specs' => [
            'Материал: carbon',
            'Тип: track fork',
            'Совместимость: 700c wheels',
            'Цвет: black',
            'Размеры: S / M / L',
            'Назначение: velodrome / track',
        ],
    ],
    'fork-vendetta' => [
        'description' => 'Жесткая трековая вилка Vendetta для fixed gear сборок с агрессивной посадкой и быстрым откликом.',
        'specs' => [
            'Материал: carbon',
            'Тип: aero track fork',
            'Совместимость: 700c wheels',
            'Цвет: black',
            'Размеры: S / M / L',
            'Назначение: fixed gear / track',
        ],
    ],
    'wheel-grancompe-tb14' => [
        'description' => 'Классический вилсет для трековых и городских велосипедов с винтажной эстетикой и надежной конструкцией. Подходит для ежедневного использования и фиксированных передач.',
        'specs' => [
            'Втулки: Gran Compe',
            'Обода: H Plus Son TB14',
            'Формат: 700c',
            'Тип покрышек: clincher / tubular',
            'Цвет: silver',
        ],
    ],
    'wheel-mack-hed-belgium' => [
        'description' => 'Легкий и жесткий вилсет для агрессивного городского катания и трековых сборок. Отличается высокой отзывчивостью и хорошим накатом.',
        'specs' => [
            'Втулки: Mack',
            'Обода: HED Belgium',
            'Формат: 700c',
            'Тип покрышек: clincher / tubular',
            'Цвет: black',
            'Назначение: track / urban',
        ],
    ],
    'wheel-hed' => [
        'description' => 'Аэродинамичный вилсет для высокоскоростных трековых и шоссейных сборок. Разработан с акцентом на снижение сопротивления и стабильность на скорости.',
        'specs' => [
            'Производитель: HED',
            'Формат: 700c',
            'Тип покрышек: clincher / tubular',
            'Материал: carbon',
            'Цвет: black',
        ],
    ],
    'wheel-phil-velocity-pink' => [
        'description' => 'Премиальный кастомный вилсет с легендарными втулками Phil Wood и яркими ободами Velocity. Подходит для уникальных городских сборок.',
        'specs' => [
            'Втулки: Phil Wood',
            'Обода: Velocity Pink',
            'Формат: 700c',
            'Тип покрышек: clincher / tubular',
            'Цвет: pink / silver',
            'Назначение: urban / fixed gear',
        ],
    ],
    'wheel-dura-ace-tb14' => [
        'description' => 'Надежный вилсет с трековыми втулками Shimano Dura Ace и классическими полированными ободами H Plus Son.',
        'specs' => [
            'Втулки: Shimano Dura Ace',
            'Обода: H Plus Son TB14',
            'Формат: 700c',
            'Тип покрышек: clincher / tubular',
            'Цвет: silver',
            'Назначение: track cycling',
        ],
    ],
    'wheel-dt-swiss' => [
        'description' => 'Современный легкий вилсет с высокой жесткостью и плавным ходом втулок. Подходит для ежедневного использования и спортивного катания.',
        'specs' => [
            'Производитель: DT Swiss',
            'Формат: 700c',
            'Тип покрышек: clincher / tubular',
            'Материал: aluminum',
            'Цвет: black',
            'Назначение: urban / performance',
        ],
    ],
    'hub-mack-silver' => [
        'description' => 'Классические трековые втулки Mack с полированным серебристым покрытием для fixed gear и трековых сборок.',
        'specs' => [
            'Производитель: Mack',
            'Материал: aluminum',
            'Цвет: silver',
            'Тип: track hubs',
            'Резьба: fixed / fixed-fixed',
            'Назначение: track / fixed gear',
        ],
    ],
    'hub-mack-black' => [
        'description' => 'Черные трековые втулки Mack с минималистичным дизайном и высокой жесткостью.',
        'specs' => [
            'Производитель: Mack',
            'Материал: aluminum',
            'Цвет: black',
            'Тип: track hubs',
            'Резьба: fixed / fixed-fixed',
            'Назначение: track / fixed gear',
        ],
    ],
    'hub-phil-wood-red' => [
        'description' => 'Премиальные втулки Phil Wood с красным анодированным покрытием и высокой надежностью.',
        'specs' => [
            'Производитель: Phil Wood',
            'Материал: aluminum',
            'Цвет: red',
            'Тип: sealed bearing hubs',
            'Резьба: fixed / fixed-fixed',
            'Назначение: track / urban riding',
        ],
    ],
    'hub-reina-black' => [
        'description' => 'Легкие трековые втулки Reina для городских и трековых велосипедов.',
        'specs' => [
            'Производитель: Reina',
            'Материал: aluminum',
            'Цвет: black',
            'Тип: track hubs',
            'Резьба: fixed / fixed-fixed',
            'Назначение: fixed gear',
        ],
    ],
    'wheel-carbon-blade' => [
        'description' => 'Карбоновое лопастное колесо для трековых велосипедов с высокой аэродинамической эффективностью.',
        'specs' => [
            'Материал: carbon',
            'Тип: track wheel',
            'Формат: clincher / tubular',
            'Цвет: black',
            'Назначение: track racing',
        ],
    ],
    'wheel-dt-swiss-8' => [
        'description' => 'Современный вилсет DT Swiss для городских и трековых сборок с высокой надежностью.',
        'specs' => [
            'Производитель: DT Swiss',
            'Материал: aluminum',
            'Тип: wheelset',
            'Формат: clincher / tubular',
            'Цвет: black',
            'Назначение: urban / track cycling',
        ],
    ],
    'accessory-mash-rack' => [
        'description' => 'Минималистичный багажник для городских и трековых велосипедов. Подходит для ежедневного использования и перевозки небольших вещей в городе.',
        'specs' => [
            'Производитель: Mash SF',
            'Материал: titanium / steel',
            'Цвет: silver',
            'Назначение: urban cycling',
            'Тип крепления: rear rack',
        ],
    ],
    'accessory-knog-bell' => [
        'description' => 'Компактный велосипедный звонок с минималистичным дизайном и громким чистым звучанием.',
        'specs' => [
            'Производитель: Knog',
            'Материал: aluminum',
            'Громкость: medium / high',
            'Цвет: black',
            'Тип: handlebar bell',
        ],
    ],
    'accessory-sugino-sticker' => [
        'description' => 'Фирменный стикер Sugino для оформления велосипеда, аксессуаров или инструментов.',
        'specs' => [
            'Производитель: Sugino',
            'Материал: vinyl',
            'Размер: 3 см / 5 см',
            'Цвет: silver / black',
        ],
    ],
    'accessory-ass-saver-fender' => [
        'description' => 'Легкое съемное крыло для защиты от воды и грязи во время городских поездок.',
        'specs' => [
            'Производитель: Ass Saver',
            'Материал: plastic',
            'Длина: 40 см / 65 см',
            'Цвет: black',
            'Тип: rear mudguard',
        ],
    ],
    'accessory-sim-works-bottle' => [
        'description' => 'Минималистичная велосипедная фляга для ежедневных поездок и тренировок.',
        'specs' => [
            'Производитель: Sim Works',
            'Объем: 500 мл / 650 мл',
            'Материал: BPA free plastic',
            'Цвет: white',
            'Назначение: cycling bottle',
        ],
    ],
    'accessory-a-frame-rack' => [
        'description' => 'Минималистичный городской багажник A-Frame для fixed gear и urban сборок.',
        'specs' => [
            'Производитель: A-Frame',
            'Материал: titanium / steel',
            'Цвет: silver',
            'Тип: rear rack',
            'Назначение: urban riding',
        ],
    ],
    'accessory-ass-saver-short' => [
        'description' => 'Компактное крыло Ass Saver для защиты от грязи и воды во время городского катания.',
        'specs' => [
            'Производитель: Ass Saver',
            'Материал: plastic',
            'Длина: 15 / 20 cm',
            'Цвет: black',
            'Тип: rear fender',
        ],
    ],
    'accessory-cinelli-sticker' => [
        'description' => 'Минималистичный виниловый стикер Cinelli для велосипедов, ноутбуков и аксессуаров.',
        'specs' => [
            'Производитель: Cinelli',
            'Материал: vinyl',
            'Размер: 2 / 3 cm',
            'Цвет: black',
            'Тип: sticker',
        ],
    ],
    'accessory-nitto-bottle-cage' => [
        'description' => 'Легкий алюминиевый флягодержатель Nitto для городских и трековых велосипедов.',
        'specs' => [
            'Производитель: Nitto',
            'Материал: aluminum',
            'Объем: 0.5 / 0.7',
            'Цвет: silver',
            'Тип: bottle cage',
        ],
    ],
    'clothes-kashimax-gloves' => [
        'description' => 'Легкие велосипедные перчатки для городского и трекового катания с удобной посадкой и минималистичным дизайном.',
        'specs' => [
            'Производитель: Kashimax',
            'Материал: synthetic fabric',
            'Размер: S / M / L',
            'Цвет: black',
            'Назначение: urban / track cycling',
        ],
    ],
    'clothes-cinelli-cap' => [
        'description' => 'Классическая велосипедная кепка Cinelli для ежедневного использования и тренировок.',
        'specs' => [
            'Производитель: Cinelli',
            'Материал: cotton',
            'Размер: S / M / L',
            'Цвет: white / black',
            'Тип: cycling cap',
        ],
    ],
    'clothes-nitto-tshirt' => [
        'description' => 'Минималистичная футболка с фирменной графикой Nitto, выполненная в стиле классических велосипедных брендов.',
        'specs' => [
            'Производитель: Nitto',
            'Материал: cotton',
            'Размер: S / M / L',
            'Цвет: white',
            'Тип: casual wear',
        ],
    ],
    'clothes-sim-works-tshirt' => [
        'description' => 'Повседневная футболка Sim Works с лаконичным дизайном и комфортной посадкой.',
        'specs' => [
            'Производитель: Sim Works',
            'Материал: cotton',
            'Размер: S / M / L',
            'Цвет: gray',
            'Тип: casual wear',
        ],
    ],
    'clothes-phil-beanie' => [
        'description' => 'Теплая минималистичная шапка для городского использования и катания в холодную погоду.',
        'specs' => [
            'Производитель: Phil Wood',
            'Материал: wool blend',
            'Размер: S / M / L',
            'Цвет: black',
            'Тип: beanie',
        ],
    ],
    'clothes-araya-cap' => [
        'description' => 'Классическая велосипедная кепка Araya для городского и трекового катания.',
        'specs' => [
            'Производитель: Araya',
            'Материал: cotton',
            'Размер: S / M / L',
            'Цвет: white',
            'Тип: cycling cap',
        ],
    ],
    'clothes-nitto-shirt' => [
        'description' => 'Легкая рубашка Nitto в минималистичном стиле для повседневного использования и городского катания.',
        'specs' => [
            'Производитель: Nitto',
            'Материал: cotton',
            'Размер: S / M / L',
            'Цвет: beige',
            'Тип: casual shirt',
        ],
    ],
    'component-vision-cranks' => [
        'description' => 'Легкие алюминиевые шатуны для трековых велосипедов, рассчитанные на высокую жесткость и стабильную передачу усилия. Подходят как для тренировок, так и для соревновательного использования на треке.',
        'specs' => [
            'Материал: алюминий',
            'Тип: трековые шатуны',
            'Посадка звезды: 144 BCD',
            'Цвет: черный',
            'Длина: 165 / 170 / 172.5 мм',
        ],
    ],
    'component-sugino-cranks' => [
        'description' => 'Классические японские трековые шатуны, известные высокой надежностью и точной обработкой деталей. Используются многими профессиональными трековыми гонщиками.',
        'specs' => [
            'Материал: алюминий',
            'Производство: Япония',
            'Посадка звезды: 144 BCD',
            'Цвет: silver',
            'Длина: 165 / 170 / 172.5 мм',
        ],
    ],
    'component-ritchey-seatpost-black' => [
        'description' => 'Легкий подседельный штырь с минималистичным дизайном и надежной фиксацией седла. Подходит для городских и трековых сборок.',
        'specs' => [
            'Материал: алюминий',
            'Цвет: black',
            'Диаметр: 27.2 мм',
            'Длина: 300 / 350 / 400 мм',
        ],
    ],
    'component-ritchey-seatpost-silver' => [
        'description' => 'Классический алюминиевый подседельный штырь в серебристом исполнении. Хорошо сочетается с винтажными и трековыми велосипедами.',
        'specs' => [
            'Материал: алюминий',
            'Цвет: silver',
            'Диаметр: 27.2 мм',
            'Длина: 300 / 350 / 400 мм',
        ],
    ],
    'component-selle-italia-saddle' => [
        'description' => 'Комфортное спортивное седло с классической формой, рассчитанное на активное катание и длительные поездки.',
        'specs' => [
            'Материал рамок: сталь',
            'Покрытие: synthetic leather',
            'Цвет: black',
            'Ширина: 130 / 145 / 153 мм',
        ],
    ],
    'component-kashimax-saddle' => [
        'description' => 'Легендарное седло в стиле old school BMX и track bikes. Часто используется в кастомных городских и трековых сборках.',
        'specs' => [
            'Материал рамок: chromoly',
            'Цвет: black',
            'Стиль: retro track',
            'Ширина: 130 / 145 / 153 мм',
        ],
    ],
    'component-mks-pedals' => [
        'description' => 'Японские педали с плавным ходом подшипников и высокой надежностью. Подходят для фиксированных передач и городских велосипедов.',
        'specs' => [
            'Материал: алюминий',
            'Производство: Япония',
            'Тип резьбы: ITA / BSA',
            'Цвет: silver',
        ],
    ],
    'component-shimano-pedals' => [
        'description' => 'Надежные педали для ежедневного использования и тренировок. Обеспечивают стабильное сцепление и долговечность.',
        'specs' => [
            'Материал: алюминий',
            'Тип резьбы: ITA / BSA',
            'Цвет: black',
            'Назначение: track / urban',
        ],
    ],
    'component-aarn-chainring' => [
        'description' => 'Высокоточная трековая звезда с жесткой конструкцией и качественной обработкой поверхности.',
        'specs' => [
            'Материал: CNC aluminum',
            'BCD: 144',
            'Цвет: silver',
            'Количество зубов: 43 / 46 / 47 / 49 / 50',
        ],
    ],
    'component-sugino-chainring' => [
        'description' => 'Классическая трековая звезда Sugino для фиксированных передач и трековых велосипедов.',
        'specs' => [
            'Материал: aluminum',
            'BCD: 144',
            'Цвет: silver',
            'Количество зубов: 43 / 46 / 47 / 49 / 50',
        ],
    ],
    'component-nitto-handlebar' => [
        'description' => 'Японский руль с высокой прочностью и классической геометрией для трековых и городских велосипедов.',
        'specs' => [
            'Материал: aluminum',
            'Производство: Япония',
            'Диаметр зажима: 25.4 / 31.8 мм',
            'Цвет: silver',
        ],
    ],
    'component-cinelli-handlebar' => [
        'description' => 'Легкий спортивный руль с современной геометрией и удобным хватом.',
        'specs' => [
            'Материал: aluminum',
            'Цвет: black',
            'Ширина: 400 / 420 / 440 мм',
        ],
    ],
    'component-nitto-stem' => [
        'description' => 'Классический алюминиевый вынос для трековых и городских велосипедов. Отличается надежной фиксацией и минималистичным дизайном.',
        'specs' => [
            'Материал: aluminum',
            'Цвет: silver',
            'Длина: 100 / 110 / 120 / 130 мм',
        ],
    ],
    'component-cinelli-stem' => [
        'description' => 'Современный вынос с жесткой конструкцией для агрессивного городского и трекового катания.',
        'specs' => [
            'Материал: aluminum',
            'Цвет: black',
            'Длина: 100 / 120 / 140 мм',
        ],
    ],
    'component-eai-cog-silver' => [
        'description' => 'Точная трековая звезда задней втулки, рассчитанная на высокие нагрузки и плавную работу трансмиссии.',
        'specs' => [
            'Материал: hardened steel',
            'Цвет: silver',
            'Количество зубов: 14 / 15 / 16 / 17 / 18 / 19',
            'Тип: fixed cog',
        ],
    ],
    'component-chris-king-headset' => [
        'description' => 'Премиальная рулевая система с высокой точностью обработки и долговечными промышленными подшипниками.',
        'specs' => [
            'Тип: threadless headset',
            'Материал: aluminum',
            'Подшипники: sealed bearings',
            'Размер: 1 inch / 1 1/8 inch',
        ],
    ],
];
$productDetails = $productDetailsMap[$product['slug']] ?? [
    'description' => '',
    'specs' => [],
];
?>

<main class="product-main">
<div class="product-page-head">
    <p class="product-breadcrumb">МАГАЗИН — <?php echo htmlspecialchars($productCategoryLabel); ?> — <?php echo htmlspecialchars($product['name']); ?></p>
</div>
<section id="product" class="product-layout">
    <div class="product-left">
        <a class="product-back" href="<?php echo htmlspecialchars($previousPageUrl); ?>"><span>&#8592;</span> НАЗАД</a>
        <img class="product-image" src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="product-right">
        <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
        <h3 class="product-price"><?php echo htmlspecialchars($productPrice); ?></h3>

        <div class="product-option">
            <label for="product-option"><?php echo htmlspecialchars($productOptionConfig['label']); ?></label>
            <select id="product-option" name="product_option">
                <option value="" selected disabled><?php echo htmlspecialchars($productOptionPlaceholder); ?></option>
                <?php foreach ($productOptionConfig['options'] as $option): ?>
                    <option value="<?php echo htmlspecialchars($option); ?>"><?php echo htmlspecialchars($option); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <p class="product-option-message" id="product-option-message" role="status" aria-live="polite"></p>

        <div class="quantity">
            <label for="product-quantity">Количество:</label>
            <input id="product-quantity" type="number" name="quantity" min="1" value="1" required="">
        </div>

        <button class="product-action" id="add-to-cart" data-product-slug="<?php echo htmlspecialchars($product['slug']); ?>">ДОБАВИТЬ В КОРЗИНУ</button>

        <div class="product-info">
            <h3><?php echo htmlspecialchars($deliveryTitle); ?></h3>
            <p><?php echo htmlspecialchars($deliveryText); ?></p>
        </div>

        <div class="product-meta">
            <button class="product-meta-item" type="button" aria-expanded="false" aria-controls="product-description">ОПИСАНИЕ <span>+</span></button>
            <div class="product-meta-panel" id="product-description" hidden>
                <?php if ($productDetails['description'] !== ''): ?>
                    <p><?php echo htmlspecialchars($productDetails['description']); ?></p>
                <?php endif; ?>
            </div>
            <button class="product-meta-item" type="button" aria-expanded="false" aria-controls="product-specs">ХАРАКТЕРИСТИКИ <span>+</span></button>
            <div class="product-meta-panel" id="product-specs" hidden>
                <?php if (!empty($productDetails['specs'])): ?>
                    <ul>
                        <?php foreach ($productDetails['specs'] as $spec): ?>
                            <li><?php echo htmlspecialchars($spec); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
</main>
<script>
    document.querySelectorAll('.product-meta-item[aria-controls]').forEach((button) => {
        button.addEventListener('click', () => {
            const panel = document.getElementById(button.getAttribute('aria-controls'));
            const icon = button.querySelector('span');
            const isOpen = button.getAttribute('aria-expanded') === 'true';

            button.setAttribute('aria-expanded', String(!isOpen));
            if (panel) {
                panel.hidden = isOpen;
            }
            if (icon) {
                icon.textContent = isOpen ? '+' : '−';
            }
        });
    });

    document.getElementById('add-to-cart').addEventListener('click', async () => {
        const addToCartButton = document.getElementById('add-to-cart');
        const productSlug = addToCartButton.dataset.productSlug;
        const productQuantity = document.getElementById('product-quantity').value;
        const productOptionSelect = document.getElementById('product-option');
        const productOption = productOptionSelect.value;
        const productOptionMessage = document.getElementById('product-option-message');

        if (!productOption) {
            productOptionMessage.textContent = 'Выберите опцию товара';
            productOptionMessage.classList.add('is-visible');
            productOptionSelect.focus();
            return;
        }

        productOptionMessage.textContent = '';
        productOptionMessage.classList.remove('is-visible');

        const data = new FormData();
        data.append('slug', productSlug);
        data.append('quantity', productQuantity);
        data.append('product_option', productOption);

        const initialButtonText = addToCartButton.textContent;
        addToCartButton.disabled = true;

        try {
            const response = await fetch('add_to_cart.php', {
                method: 'POST',
                body: data
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error('Add to cart failed');
            }

            const cartCounter = document.getElementById('cart-count');
            if (cartCounter && typeof result.cart_count !== 'undefined') {
                const nextCount = Number(result.cart_count) || 0;
                cartCounter.textContent = String(nextCount);
                cartCounter.classList.toggle('is-empty', nextCount <= 0);
            }

            addToCartButton.textContent = 'ДОБАВЛЕНО';
            setTimeout(() => {
                addToCartButton.textContent = initialButtonText;
            }, 1200);
        } catch (error) {
            console.error('ERROR', error);
            addToCartButton.textContent = initialButtonText;
        } finally {
            addToCartButton.disabled = false;
        }
    });
</script>

<?php require_once "../components/footer.php"; ?>
