<?php

// Открываем PHP-сессию для доступа к корзине и токенам оформления.
session_start();

// Подключаем загрузчик Composer, чтобы использовать библиотеку Stripe.
require_once __DIR__ . '/../vendor/autoload.php';

// Подключаем каталог и функцию поиска товара по slug.
require_once __DIR__ . '/../data/products.php';

// Обработчик принимает только отправку формы методом POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Сообщаем, что использованный HTTP-метод не поддерживается.
    http_response_code(405);

    // Указываем разрешенный для этого обработчика метод.
    header('Allow: POST');

    // Завершаем запрос без создания заказа.
    exit('Откройте форму оформления заказа.');
}

// Пока подключения к базе нет.
// Переменная понадобится в catch для проверки возможности отката.
$pdo = null;

try {
    // Получаем CSRF-токен из скрытого поля формы.
    $csrf = $_POST['csrf_token'] ?? null;

    // Получаем токен конкретной формы оформления.
    $token = $_POST['checkout_token'] ?? null;

    // Проверяем, что форма относится к текущей сессии и еще действительна.
    if (
        !is_string($csrf) // CSRF-токен должен быть строкой.
        || empty($_SESSION['checkout_csrf']) // В сессии должен быть токен.
        || !hash_equals($_SESSION['checkout_csrf'], $csrf) // Токены должны совпадать.
        || !is_string($token) // Токен оформления должен быть строкой.
        || !isset($_SESSION['checkout_tokens'][$token]) // Он должен быть выдан этой сессии.
        || $_SESSION['checkout_tokens'][$token] < time() - 7200 // Срок — два часа.
    ) {
        // Ошибка попадет в catch; заказ на этом этапе еще не создан.
        throw new InvalidArgumentException(
            'Форма устарела. Откройте оформление заново.'
        );
    }

    // Здесь соберем проверенные данные покупателя.
    $buyer = [];

    // Обрабатываем только три ожидаемых поля формы.
    foreach (['last_name', 'first_name', 'phone_number'] as $field) {
        // Получаем значение текущего поля либо null, если оно отсутствует.
        $value = $_POST[$field] ?? null;

        // Проверяем тип, заполненность и длину значения.
        if (
            !is_string($value) // Не принимаем массивы и другие типы.
            || trim($value) === '' // Строка не должна состоять только из пробелов.
            || strlen($value) > 200 // Максимальная длина — 200 байт.
        ) {
            throw new InvalidArgumentException(
                'Заполните фамилию, имя и телефон.'
            );
        }

        // Убираем пробелы по краям и сохраняем проверенное значение.
        $buyer[$field] = trim($value);
    }

    // Берем корзину из серверной сессии, а не из отправленной формы.
    $cart = $_SESSION['cart'] ?? [];

    // Корзина должна быть непустым массивом максимум из 100 позиций.
    if (!is_array($cart) || !$cart || count($cart) > 100) {
        throw new InvalidArgumentException('Проверьте корзину.');
    }

    // Данные товаров для сохранения в таблицу order_items.
    $items = [];

    // Позиции заказа в формате Stripe Checkout.
    $lines = [];

    // Состав корзины для вычисления ее подписи.
    $signature = [];

    // Общая сумма в минимальных единицах валюты — копейках.
    $totalMinor = 0;

    // Общее количество единиц товаров, а не количество строк корзины.
    $totalItems = 0;

    // Проверяем и преобразуем каждую позицию корзины.
    foreach ($cart as $item) {
        // Позиция должна быть массивом со строковыми slug и вариантом товара.
        if (
            !is_array($item)
            || !is_string($item['slug'] ?? null)
            || !is_string($item['product_option'] ?? null)
        ) {
            throw new InvalidArgumentException(
                'Некорректный товар в корзине.'
            );
        }

        // Получаем актуальные данные товара из серверного каталога.
        $product = cadenceProductBySlug($item['slug']);

        // Проверяем количество как целое число.
        // При неудачной проверке получим false.
        $qty = filter_var(
            $item['quantity'] ?? null,
            FILTER_VALIDATE_INT
        );

        // Убираем пробелы по краям варианта товара.
        $option = trim($item['product_option']);

        // Проверяем цену из каталога как целое число рублей.
        // Если товар не найден, сразу используем false.
        $price = $product
            ? filter_var($product['price'], FILTER_VALIDATE_INT)
            : false;

        // Проверяем ограничения текущей реализации магазина.
        if (
            !$product // Товар должен существовать в каталоге.
            || $qty === false // Количество должно быть целым.
            || $qty < 1 // Минимум одна единица.
            || $qty > 99 // Максимум 99 единиц в позиции.
            || $option === '' // Вариант товара должен быть указан.
            || strlen($option) > 300 // Длина варианта — максимум 300 байт.
            || $price === false // Цена должна быть целым числом.
            || $price < 1 // Бесплатные товары здесь не поддерживаются.
            || $price > 999999 // Ограничение цены единицы товара.
        ) {
            throw new InvalidArgumentException(
                'Проверьте товар, вариант и количество.'
            );
        }

        // Сохраняем снимок названия, цены, количества и варианта для базы.
        // Порядок значений соответствует INSERT INTO order_items ниже.
        $items[] = [$product['name'], $price, $qty, $option];

        // Для подписи корзины используем slug, количество и вариант.
        $signature[] = [$item['slug'], $qty, $option];

        // Переводим рубли в копейки и прибавляем стоимость всей позиции.
        $totalMinor += $price * 100 * $qty;

        // Прибавляем количество единиц текущего товара.
        $totalItems += $qty;

        // Формируем одну позицию для платежной страницы Stripe.
        $lines[] = [
            'price_data' => [
                // Все позиции текущей интеграции оплачиваются в рублях.
                'currency' => 'rub',

                // Цена одной единицы в копейках, без умножения на количество.
                'unit_amount' => $price * 100,

                // Описание товара, которое будет показано в Checkout.
                'product_data' => [
                    'name' => $product['name'],
                    'description' => 'Вариант: ' . $option,
                ],
            ],

            // Количество передается отдельно от цены единицы.
            'quantity' => $qty,
        ];
    }

    // Ограничиваем тестовый заказ суммой меньше миллиона рублей.
    if ($totalMinor > 99999999) {
        throw new InvalidArgumentException(
            'Выберите сумму меньше 1 000 000 ₽.'
        );
    }

    // Сортируем состав корзины для стабильной подписи.
    // Простая перестановка одинаковых позиций не должна менять подпись.
    sort($signature);

    // Получаем SHA-256-хеш состава корзины.
    // Позднее он позволит проверить, что очищается именно оплаченная корзина.
    $cartHash = hash(
        'sha256',
        json_encode(
            $signature,
            // Ошибка преобразования в JSON должна вызвать исключение.
            // Кириллицу сохраняем без Unicode-экранирования.
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
        )
    );

    // Получаем хеш товаров и данных покупателя.
    // Повторная отправка одной формы должна содержать те же данные.
    $requestHash = hash(
        'sha256',
        json_encode([$items, $signature, $buyer], JSON_THROW_ON_ERROR)
    );

    // Загружаем локальные настройки: адрес сайта, ключ Stripe и параметры базы.
    $config = require __DIR__ . '/../config/local.php';

    // Эта версия обработчика допускает только тестовый секретный ключ.
    if (!str_starts_with($config['stripe_secret_key'] ?? '', 'sk_test_')) {
        throw new RuntimeException(
            'В config/local.php нужен тестовый ключ sk_test_.'
        );
    }

    // Создаем клиент для запросов к Stripe API.
    $stripe = new \Stripe\StripeClient($config['stripe_secret_key']);

    // Получаем настроенное PDO-подключение к PostgreSQL.
    $pdo = require __DIR__ . '/../config/db.php';

    // Начинаем транзакцию создания заказа.
    // До commit() связанные изменения можно откатить вместе.
    $pdo->beginTransaction();

    // Подготавливаем блокировку, связанную с токеном этой формы.
    // Одновременные запросы с одинаковым токеном будут ждать друг друга.
    $lock = $pdo->prepare(
        'SELECT pg_advisory_xact_lock(hashtext(:token))'
    );

    // Получаем блокировку; она освободится при завершении транзакции.
    $lock->execute(['token' => $token]);

    // Ищем заказ, который уже мог быть создан этой формой.
    $get = $pdo->prepare(
        'SELECT * FROM orders WHERE checkout_token = :token'
    );

    // Передаем токен отдельно от текста SQL-запроса.
    $get->execute(['token' => $token]);

    // Получаем найденную строку заказа либо false.
    $order = $get->fetch();

    // Если заказ существует, повторно создавать его не нужно.
    if ($order) {
        // Проверяем, что при повторе данные покупателя и товаров не изменились.
        if (!hash_equals($order['checkout_request_hash'], $requestHash)) {
            throw new InvalidArgumentException(
                'Данные изменились. Откройте новую форму оформления.'
            );
        }
    } else {
        // Для нового заказа создаем запись покупателя.
        // RETURNING id возвращает идентификатор вставленной строки.
        $insert = $pdo->prepare(
            'INSERT INTO customers (last_name, first_name, phone_number)
             VALUES (:last_name, :first_name, :phone_number)
             RETURNING id'
        );

        // Имена ключей массива buyer совпадают с параметрами SQL-запроса.
        $insert->execute($buyer);

        // Получаем идентификатор созданного покупателя.
        $customerId = $insert->fetchColumn();

        // Создаем заказ с ожидающей оплатой и служебными данными.
        // Сам факт создания заказа еще не означает успешную оплату.
        $insert = $pdo->prepare(
            "INSERT INTO orders (
                customer_id, total_items, total_price,
                payment_status, payment_currency, payment_amount_minor,
                checkout_token, checkout_request_hash,
                cart_signature, payment_access_token
             )
             VALUES (
                :customer, :quantity, :total,
                'pending', 'rub', :minor,
                :token, :hash, :cart, :access
             )
             RETURNING id"
        );

        // Передаем значения для создания заказа.
        $insert->execute([
            // Связываем заказ с созданной записью покупателя.
            'customer' => $customerId,

            // Общее количество единиц товаров.
            'quantity' => $totalItems,

            // Сумма в рублях; каталог сейчас содержит целые рублевые цены.
            'total' => intdiv($totalMinor, 100),

            // Та же сумма в копейках для сравнения с платежом Stripe.
            'minor' => $totalMinor,

            // Токен формы, создавшей заказ.
            'token' => $token,

            // Хеш данных для проверки повторной отправки.
            'hash' => $requestHash,

            // Подпись корзины для последующей очистки.
            'cart' => $cartHash,

            // Случайный токен доступа к странице результата заказа.
            'access' => bin2hex(random_bytes(32)),
        ]);

        // Получаем идентификатор нового заказа.
        $id = $insert->fetchColumn();

        // Подготавливаем запрос сохранения отдельных товаров заказа.
        // Здесь используются позиционные параметры: пять знаков вопроса.
        $insert = $pdo->prepare(
            'INSERT INTO order_items (
                order_id, product_name, product_price,
                quantity, product_option
             )
             VALUES (?, ?, ?, ?, ?)'
        );

        // Сохраняем каждую проверенную позицию корзины.
        foreach ($items as $item) {
            // Добавляем id заказа перед названием, ценой, количеством и вариантом.
            $insert->execute(array_merge([$id], $item));
        }

        // Убираем завершающий слеш, чтобы правильно собрать ссылки возврата.
        $baseUrl = rtrim($config['app_url'], '/');

        // Подготавливаем параметры создания платежной сессии Stripe.
        $params = [
            // Разовая оплата, без подписки.
            'mode' => 'payment',

            // Русский язык интерфейса Checkout.
            'locale' => 'ru',

            // Проверенные товары, цены и количества.
            'line_items' => $lines,

            // Связываем Checkout Session с номером заказа в нашей базе.
            'client_reference_id' => (string) $id,

            // Служебные данные для проверки платежа при получении результата.
            'metadata' => [
                // Метка позволяет отличить платежи нашего магазина.
                'integration' => 'cadence_connections',

                // Номер заказа для поиска записи в PostgreSQL.
                'order_id' => (string) $id,

                // Дополнительная связь платежа с исходной формой.
                'checkout_token' => $token,
            ],

            // После завершения Checkout браузер вернется на страницу результата.
            // Stripe заменит {CHECKOUT_SESSION_ID} настоящим id сессии.
            'success_url' => $baseUrl
                . '/pages/payment_success.php?session_id={CHECKOUT_SESSION_ID}',

            // При возврате из Checkout без завершения покупатель попадет в корзину.
            'cancel_url' => $baseUrl . '/pages/cart.php',
        ];

        // Сохраняем исходные параметры оплаты в JSONB-столбец заказа.
        // Повторная попытка использует эти же параметры.
        $save = $pdo->prepare(
            'UPDATE orders
             SET stripe_checkout_payload = CAST(:payload AS jsonb)
             WHERE id = :id'
        );

        // Преобразуем массив параметров в JSON и записываем его в заказ.
        $save->execute([
            'payload' => json_encode($params, JSON_THROW_ON_ERROR),
            'id' => $id,
        ]);

        // Повторно читаем заказ после сохранения всех служебных данных.
        $get->execute(['token' => $token]);

        // Теперь order содержит полную запись нового заказа.
        $order = $get->fetch();
    }

    // Завершаем транзакцию до сетевого обращения к Stripe.
    // Заказ остается в базе, даже если внешний запрос временно не удастся.
    $pdo->commit();

    // Запоминаем право этой PHP-сессии открыть страницу результата заказа.
    // payment_success.php сравнит это значение с токеном в базе.
    $_SESSION['stripe_orders'][$order['id']] =
        $order['payment_access_token'];

    // Если Checkout Session уже создана, получаем ее актуальное состояние.
    if (!empty($order['stripe_session_id'])) {
        $checkout = $stripe->checkout->sessions->retrieve(
            $order['stripe_session_id'],
            []
        );
    } else {
        // Восстанавливаем параметры оплаты из сохраненного JSON.
        $params = json_decode(
            $order['stripe_checkout_payload'],
            true, // Получаем PHP-массивы вместо обычных объектов.
            512, // Максимальная глубина разбора JSON.
            JSON_THROW_ON_ERROR // При ошибке разбора выбрасываем исключение.
        );

        // Создаем Checkout Session с ключом повторяемого запроса.
        // При повторе с тем же ключом Stripe может вернуть прежний результат.
        $checkout = $stripe->checkout->sessions->create($params, [
            'idempotency_key' => 'cadence_order_'
                . $order['id'] . '_' . $token,
        ]);
    }

    // Проверяем ответ Stripe перед сохранением ссылки и перенаправлением.
    if (
        $checkout->livemode // Реальные платежи эта версия не принимает.
        || $checkout->mode !== 'payment' // Ожидаем разовую оплату.
        || $checkout->currency !== $order['payment_currency'] // Валюта должна совпадать.
        || (int) $checkout->amount_total !== (int) $order['payment_amount_minor'] // Сумма должна совпадать.
        || (string) $checkout->client_reference_id !== (string) $order['id'] // Проверяем номер заказа.
        || (string) $checkout->metadata->checkout_token !== $token // Проверяем токен формы.
        || !str_starts_with($checkout->id, 'cs_test_') // Ожидаем тестовую Checkout Session.
    ) {
        throw new RuntimeException(
            'Stripe вернул несоответствующий заказу платеж.'
        );
    }

    // Сохраняем связь заказа с созданной или полученной сессией Stripe.
    $save = $pdo->prepare(
        'UPDATE orders SET stripe_session_id = :session WHERE id = :id'
    );

    // Записываем id Stripe в строку нужного заказа.
    $save->execute([
        'session' => $checkout->id,
        'id' => $order['id'],
    ]);

    // Завершенную сессию повторно не открываем для оплаты.
    // Страница результата отдельно проверит фактический статус платежа.
    if ($checkout->status === 'complete') {
        // Перенаправляем на результат, безопасно кодируя id для URL.
        header(
            'Location: payment_success.php?session_id='
                . rawurlencode($checkout->id),
            true, // Заменяем предыдущий заголовок Location, если он был.
            303 // После POST браузер перейдет по ссылке методом GET.
        );

        // Завершаем обработчик после отправки перенаправления.
        exit;
    }

    // Для перехода к оплате нужна открытая сессия с платежным URL.
    if ($checkout->status !== 'open' || !$checkout->url) {
        throw new InvalidArgumentException(
            'Ссылка истекла. Откройте новую форму оформления.'
        );
    }

    // Отправляем покупателя на платежную страницу Stripe.
    header('Location: ' . $checkout->url, true, 303);

    // Завершаем запрос; корзину здесь не очищаем.
    exit;

} catch (Throwable $e) {
    // Откатываем изменения только при наличии незавершенной транзакции.
    // Уже выполненный commit() и действия в Stripe этим не отменяются.
    if ($pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Ошибки входных данных возвращают 400, остальные ошибки — 503.
    http_response_code(
        $e instanceof InvalidArgumentException ? 400 : 503
    );

    // Показываем текст ошибки с экранированием HTML.
    // Перед публикацией технические ошибки стоит перенести в серверный журнал.
    echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');

    // Даем посетителю ссылку для повторного открытия формы.
    echo '<p><a href="checkout_form.php">Вернуться к оформлению</a></p>';
}