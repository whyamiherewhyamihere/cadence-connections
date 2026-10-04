<?php

if (!function_exists('cadenceProducts')) {
    function cadenceProducts(): array
    {
        static $products = [
            'frameset-veloci' => [
                'slug' => 'frameset-veloci',
                'name' => 'Фреймсет Veloci',
                'price' => 50000,
                'image' => '../images/framesets1.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-vendetta' => [
                'slug' => 'frameset-vendetta',
                'name' => 'Фреймсет Vendetta',
                'price' => 80000,
                'image' => '../images/framesets2.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-skream' => [
                'slug' => 'frameset-skream',
                'name' => 'Фреймсет Skream',
                'price' => 140000,
                'image' => '../images/framesets3.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-thomson' => [
                'slug' => 'frameset-thomson',
                'name' => 'Фреймсет Thomson',
                'price' => 75000,
                'image' => '../images/framesets4.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-dolan' => [
                'slug' => 'frameset-dolan',
                'name' => 'Фреймсет Dolan',
                'price' => 160000,
                'image' => '../images/framesets5.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-gan-well-pro' => [
                'slug' => 'frameset-gan-well-pro',
                'name' => 'Фреймсет Gan Well Pro',
                'price' => 180000,
                'image' => '../images/framesets6.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-soma' => [
                'slug' => 'frameset-soma',
                'name' => 'Фреймсет Soma',
                'price' => 65000,
                'image' => '../images/framesets7.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-soyo' => [
                'slug' => 'frameset-soyo',
                'name' => 'Фреймсет Soyo',
                'price' => 200000,
                'image' => '../images/framesets8.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-ritchey' => [
                'slug' => 'frameset-ritchey',
                'name' => 'Фреймсет Ritchey',
                'price' => 90000,
                'image' => '../images/framesets9.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-cinelli' => [
                'slug' => 'frameset-cinelli',
                'name' => 'Фреймсет Cinelli',
                'price' => 120000,
                'image' => '../images/framesets10.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-tsunami' => [
                'slug' => 'frameset-tsunami',
                'name' => 'Фреймсет Tsunami',
                'price' => 40000,
                'image' => '../images/framesets11.webp',
                'category_page' => 'index2.php',
            ],
            'frameset-skream-12' => [
                'slug' => 'frameset-skream-12',
                'name' => 'Фреймсет Skream',
                'price' => 150000,
                'image' => '../images/framesets12.webp',
                'category_page' => 'index2.php',
            ],
            'fork-surly' => [
                'slug' => 'fork-surly',
                'name' => 'Вилка Surly',
                'price' => 15000,
                'image' => '../images/forks1.webp',
                'category_page' => 'index2.php',
            ],
            'fork-a-frame' => [
                'slug' => 'fork-a-frame',
                'name' => 'Вилка A-Frame',
                'price' => 18000,
                'image' => '../images/forks2.webp',
                'category_page' => 'index2.php',
            ],
            'fork-alpina' => [
                'slug' => 'fork-alpina',
                'name' => 'Вилка Alpina',
                'price' => 24000,
                'image' => '../images/forks3.webp',
                'category_page' => 'index2.php',
            ],
            'fork-vendetta' => [
                'slug' => 'fork-vendetta',
                'name' => 'Вилка Vendetta',
                'price' => 30000,
                'image' => '../images/forks4.webp',
                'category_page' => 'index2.php',
            ],

            'component-chris-king-headset' => [
                'slug' => 'component-chris-king-headset',
                'name' => 'Рулевая Chris King',
                'price' => 20000,
                'image' => '../images/components1.webp',
                'category_page' => 'index3.php',
            ],
            'component-eai-cog-silver' => [
                'slug' => 'component-eai-cog-silver',
                'name' => 'Ког EAI Silver',
                'price' => 9000,
                'image' => '../images/components2.webp',
                'category_page' => 'index3.php',
            ],
            'component-cinelli-stem' => [
                'slug' => 'component-cinelli-stem',
                'name' => 'Вынос Cinelli',
                'price' => 12000,
                'image' => '../images/components3.webp',
                'category_page' => 'index3.php',
            ],
            'component-nitto-stem' => [
                'slug' => 'component-nitto-stem',
                'name' => 'Вынос Nitto',
                'price' => 13000,
                'image' => '../images/components4.webp',
                'category_page' => 'index3.php',
            ],
            'component-cinelli-handlebar' => [
                'slug' => 'component-cinelli-handlebar',
                'name' => 'Руль Cinelli',
                'price' => 9800,
                'image' => '../images/components5.webp',
                'category_page' => 'index3.php',
            ],
            'component-nitto-handlebar' => [
                'slug' => 'component-nitto-handlebar',
                'name' => 'Руль Nitto',
                'price' => 12500,
                'image' => '../images/components6.webp',
                'category_page' => 'index3.php',
            ],
            'component-sugino-chainring' => [
                'slug' => 'component-sugino-chainring',
                'name' => 'Звезда Sugino',
                'price' => 33000,
                'image' => '../images/components7.webp',
                'category_page' => 'index3.php',
            ],
            'component-aarn-chainring' => [
                'slug' => 'component-aarn-chainring',
                'name' => 'Звезда Aarn',
                'price' => 27000,
                'image' => '../images/components8.webp',
                'category_page' => 'index3.php',
            ],
            'component-shimano-pedals' => [
                'slug' => 'component-shimano-pedals',
                'name' => 'Педали Shimano',
                'price' => 3000,
                'image' => '../images/components9.webp',
                'category_page' => 'index3.php',
            ],
            'component-mks-pedals' => [
                'slug' => 'component-mks-pedals',
                'name' => 'Педали Mks',
                'price' => 4500,
                'image' => '../images/components10.webp',
                'category_page' => 'index3.php',
            ],
            'component-kashimax-saddle' => [
                'slug' => 'component-kashimax-saddle',
                'name' => 'Седло Kashimax',
                'price' => 7460,
                'image' => '../images/components11.webp',
                'category_page' => 'index3.php',
            ],
            'component-selle-italia-saddle' => [
                'slug' => 'component-selle-italia-saddle',
                'name' => 'Седло Selle Italia',
                'price' => 9000,
                'image' => '../images/components12.webp',
                'category_page' => 'index3.php',
            ],
            'component-ritchey-seatpost-silver' => [
                'slug' => 'component-ritchey-seatpost-silver',
                'name' => 'Подседел Ritchey Silver',
                'price' => 11600,
                'image' => '../images/components13.webp',
                'category_page' => 'index3.php',
            ],
            'component-ritchey-seatpost-black' => [
                'slug' => 'component-ritchey-seatpost-black',
                'name' => 'Подседел Ritchey Black',
                'price' => 11600,
                'image' => '../images/components14.webp',
                'category_page' => 'index3.php',
            ],
            'component-sugino-cranks' => [
                'slug' => 'component-sugino-cranks',
                'name' => 'Шатуны Sugino',
                'price' => 35000,
                'image' => '../images/components15.webp',
                'category_page' => 'index3.php',
            ],
            'component-vision-cranks' => [
                'slug' => 'component-vision-cranks',
                'name' => 'Шатуны Vision',
                'price' => 28000,
                'image' => '../images/components16.webp',
                'category_page' => 'index3.php',
            ],

            'wheel-grancompe-tb14' => [
                'slug' => 'wheel-grancompe-tb14',
                'name' => 'Вилсет GranCompe Tb14',
                'price' => 40000,
                'image' => '../images/wheels1.webp',
                'category_page' => 'index4.php',
            ],
            'wheel-mack-hed-belgium' => [
                'slug' => 'wheel-mack-hed-belgium',
                'name' => 'Вилсет Mack Hed Belgium',
                'price' => 60000,
                'image' => '../images/wheels2.webp',
                'category_page' => 'index4.php',
            ],
            'wheel-hed' => [
                'slug' => 'wheel-hed',
                'name' => 'Вилсет Hed',
                'price' => 90000,
                'image' => '../images/wheels3.webp',
                'category_page' => 'index4.php',
            ],
            'wheel-phil-velocity-pink' => [
                'slug' => 'wheel-phil-velocity-pink',
                'name' => 'Вилсет Phil Velocity Pink',
                'price' => 70000,
                'image' => '../images/wheels4.webp',
                'category_page' => 'index4.php',
            ],
            'wheel-dura-ace-tb14' => [
                'slug' => 'wheel-dura-ace-tb14',
                'name' => 'Вилсет Dura Ace Tb14',
                'price' => 50000,
                'image' => '../images/wheels5.webp',
                'category_page' => 'index4.php',
            ],
            'wheel-dt-swiss' => [
                'slug' => 'wheel-dt-swiss',
                'name' => 'Вилсет Dt Swiss',
                'price' => 75000,
                'image' => '../images/wheels6.webp',
                'category_page' => 'index4.php',
            ],
            'hub-mack-silver' => [
                'slug' => 'hub-mack-silver',
                'name' => 'Втулки Mack Silver',
                'price' => 35000,
                'image' => '../images/hubs1.webp',
                'category_page' => 'index4.php',
            ],
            'hub-mack-black' => [
                'slug' => 'hub-mack-black',
                'name' => 'Втулки Mack Black',
                'price' => 37000,
                'image' => '../images/hubs2.webp',
                'category_page' => 'index4.php',
            ],
            'hub-phil-wood-red' => [
                'slug' => 'hub-phil-wood-red',
                'name' => 'Втулки Phil Wood Red',
                'price' => 50000,
                'image' => '../images/hubs3.webp',
                'category_page' => 'index4.php',
            ],
            'hub-reina-black' => [
                'slug' => 'hub-reina-black',
                'name' => 'Втулки Reina Black',
                'price' => 30000,
                'image' => '../images/hubs4.webp',
                'category_page' => 'index4.php',
            ],
            'wheel-carbon-blade' => [
                'slug' => 'wheel-carbon-blade',
                'name' => 'Лопасть карбоновая',
                'price' => 40000,
                'image' => '../images/wheels7.webp',
                'category_page' => 'index4.php',
            ],
            'wheel-dt-swiss-8' => [
                'slug' => 'wheel-dt-swiss-8',
                'name' => 'Вилсет DT Swiss',
                'price' => 75000,
                'image' => '../images/wheels8.webp',
                'category_page' => 'index4.php',
            ],

            'accessory-mash-rack' => [
                'slug' => 'accessory-mash-rack',
                'name' => 'Багажник Mash Sf',
                'price' => 30000,
                'image' => '../images/accessories1.webp',
                'category_page' => 'index5.php',
            ],
            'accessory-knog-bell' => [
                'slug' => 'accessory-knog-bell',
                'name' => 'Звонок Knog',
                'price' => 2700,
                'image' => '../images/accessories2.webp',
                'category_page' => 'index5.php',
            ],
            'accessory-sugino-sticker' => [
                'slug' => 'accessory-sugino-sticker',
                'name' => 'Стикер Sugino',
                'price' => 2000,
                'image' => '../images/accessories3.webp',
                'category_page' => 'index5.php',
            ],
            'accessory-ass-saver-fender' => [
                'slug' => 'accessory-ass-saver-fender',
                'name' => 'Крыло Ass Saver',
                'price' => 1500,
                'image' => '../images/accessories4.webp',
                'category_page' => 'index5.php',
            ],
            'accessory-sim-works-bottle' => [
                'slug' => 'accessory-sim-works-bottle',
                'name' => 'Фляга Sim Works',
                'price' => 1600,
                'image' => '../images/accessories5.webp',
                'category_page' => 'index5.php',
            ],
            'accessory-a-frame-rack' => [
                'slug' => 'accessory-a-frame-rack',
                'name' => 'Багажник A-Frame',
                'price' => 20000,
                'image' => '../images/accessories6.webp',
                'category_page' => 'index5.php',
            ],
            'accessory-ass-saver-short' => [
                'slug' => 'accessory-ass-saver-short',
                'name' => 'Крыло Ass Saver Short',
                'price' => 1500,
                'image' => '../images/accessories7.webp',
                'category_page' => 'index5.php',
            ],
            'accessory-cinelli-sticker' => [
                'slug' => 'accessory-cinelli-sticker',
                'name' => 'Стикер Cinelli',
                'price' => 2000,
                'image' => '../images/accessories8.webp',
                'category_page' => 'index5.php',
            ],
            'accessory-nitto-bottle-cage' => [
                'slug' => 'accessory-nitto-bottle-cage',
                'name' => 'Флягодержатель Nitto',
                'price' => 8300,
                'image' => '../images/accessories9.webp',
                'category_page' => 'index5.php',
            ],

            'clothes-kashimax-gloves' => [
                'slug' => 'clothes-kashimax-gloves',
                'name' => 'Перчатки Kashimax',
                'price' => 3500,
                'image' => '../images/clothes1.webp',
                'category_page' => 'index7.php',
            ],
            'clothes-cinelli-cap' => [
                'slug' => 'clothes-cinelli-cap',
                'name' => 'Кепка Cinelli',
                'price' => 3500,
                'image' => '../images/clothes2.webp',
                'category_page' => 'index7.php',
            ],
            'clothes-nitto-tshirt' => [
                'slug' => 'clothes-nitto-tshirt',
                'name' => 'Футболка Nitto',
                'price' => 5000,
                'image' => '../images/clothes3.webp',
                'category_page' => 'index7.php',
            ],
            'clothes-sim-works-tshirt' => [
                'slug' => 'clothes-sim-works-tshirt',
                'name' => 'Футболка Sim Works',
                'price' => 5000,
                'image' => '../images/clothes4.webp',
                'category_page' => 'index7.php',
            ],
            'clothes-phil-beanie' => [
                'slug' => 'clothes-phil-beanie',
                'name' => 'Шапка Phil',
                'price' => 4000,
                'image' => '../images/clothes5.webp',
                'category_page' => 'index7.php',
            ],
            'clothes-araya-cap' => [
                'slug' => 'clothes-araya-cap',
                'name' => 'Кепка Araya',
                'price' => 3500,
                'image' => '../images/clothes6.webp',
                'category_page' => 'index7.php',
            ],
            'clothes-nitto-shirt' => [
                'slug' => 'clothes-nitto-shirt',
                'name' => 'Рубашка Nitto',
                'price' => 7000,
                'image' => '../images/clothes7.webp',
                'category_page' => 'index7.php',
            ],
        ];

        return $products;
    }
}

if (!function_exists('cadenceCatalogPageSlugs')) {
    function cadenceCatalogPageSlugs(): array
    {
        return [
            'index2.php' => [
                'frameset-veloci',
                'frameset-vendetta',
                'frameset-skream',
                'frameset-thomson',
                'frameset-dolan',
                'frameset-gan-well-pro',
                'frameset-soma',
                'frameset-soyo',
                'frameset-ritchey',
                'frameset-cinelli',
                'frameset-tsunami',
                'frameset-skream-12',
                'fork-surly',
                'fork-a-frame',
                'fork-alpina',
                'fork-vendetta',
            ],
            'index3.php' => [
                'component-chris-king-headset',
                'component-eai-cog-silver',
                'component-cinelli-stem',
                'component-nitto-stem',
                'component-cinelli-handlebar',
                'component-nitto-handlebar',
                'component-sugino-chainring',
                'component-aarn-chainring',
                'component-shimano-pedals',
                'component-mks-pedals',
                'component-kashimax-saddle',
                'component-selle-italia-saddle',
                'component-ritchey-seatpost-silver',
                'component-ritchey-seatpost-black',
                'component-sugino-cranks',
                'component-vision-cranks',
            ],
            'index4.php' => [
                'wheel-grancompe-tb14',
                'wheel-mack-hed-belgium',
                'wheel-hed',
                'wheel-phil-velocity-pink',
                'wheel-dura-ace-tb14',
                'wheel-dt-swiss',
                'hub-mack-silver',
                'hub-mack-black',
                'hub-phil-wood-red',
                'hub-reina-black',
                'wheel-carbon-blade',
                'wheel-dt-swiss-8',
            ],
            'index5.php' => [
                'accessory-mash-rack',
                'accessory-knog-bell',
                'accessory-sugino-sticker',
                'accessory-ass-saver-fender',
                'accessory-sim-works-bottle',
                'accessory-a-frame-rack',
                'accessory-ass-saver-short',
                'accessory-cinelli-sticker',
                'accessory-nitto-bottle-cage',
            ],
            'index6.php' => [
                'frameset-thomson',
                'component-cinelli-handlebar',
                'component-aarn-chainring',
                'accessory-ass-saver-fender',
                'wheel-hed',
                'component-sugino-cranks',
            ],
            'index7.php' => [
                'clothes-kashimax-gloves',
                'clothes-cinelli-cap',
                'clothes-nitto-tshirt',
                'clothes-sim-works-tshirt',
                'clothes-phil-beanie',
                'clothes-araya-cap',
                'clothes-nitto-shirt',
            ],
        ];
    }
}

if (!function_exists('cadenceProductsForPage')) {
    function cadenceProductsForPage(string $page): array
    {
        $products = cadenceProducts();
        $slugs = cadenceCatalogPageSlugs()[$page] ?? [];
        $items = [];

        foreach ($slugs as $slug) {
            if (isset($products[$slug])) {
                $items[] = $products[$slug];
            }
        }

        return $items;
    }
}

if (!function_exists('cadenceProductBySlug')) {
    function cadenceProductBySlug(string $slug): ?array
    {
        $products = cadenceProducts();
        return $products[$slug] ?? null;
    }
}
