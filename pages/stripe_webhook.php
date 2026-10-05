<?php

// Подключаем загрузчик Composer и классы библиотеки Stripe.
require_once __DIR__ . '/../vendor/autoload.php';

// Подключаем общие функции проверки платежа и обновления заказа.
require_once __DIR__ . '/../lib/stripe_payment.php';

// Webhook принимает уведомления только методом POST.
// PHP-сессия покупателя здесь не используется: запрос приходит от Stripe.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Возвращаем ошибку неподдерживаемого HTTP-метода.
    http_response_code(405);

    // Указываем допустимый метод.
    header('Allow: POST');

    // Завершаем запрос без обработки события.
    exit;
}

// Первый блок: загружаем настройки и проверяем подлинность уведомления.
try {
    // Получаем локальные настройки интеграции.
    $config = require __DIR__ . '/../config/local.php';

    // Получаем секрет подписи webhook.
    // Это отдельный секрет whsec_..., а не ключ Stripe API sk_test_....
    $secret = $config['stripe_webhook_secret'] ?? '';

    // Проверяем, что секрет задан с ожидаемым префиксом.
    if (!str_starts_with($secret, 'whsec_')) {
        throw new RuntimeException('Webhook secret is not configured.');
    }

    // Проверяем подпись и разбираем уведомление средствами библиотеки Stripe.
    // Тело запроса передаем в исходном виде, без изменения JSON.
    $event = \Stripe\Webhook::constructEvent(
        // Получаем исходное тело HTTP-запроса.
        file_get_contents('php://input'),

        // Получаем подпись из HTTP-заголовка Stripe-Signature.
        $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '',

        // Используем секрет именно того webhook, который доставляет запрос.
        $secret
    );

} catch (
    // Подпись отсутствует, неверна или не прошла проверку.
    \Stripe\Exception\SignatureVerificationException

    // Либо тело уведомления невозможно корректно разобрать.
    | UnexpectedValueException $e
) {
    // Отклоняем неподтвержденное или некорректное уведомление.
    http_response_code(400);

    // Завершаем запрос до обращения к базе.
    exit('Invalid signature or payload');

} catch (Throwable $e) {
    // Записываем тип ошибки подготовки обработчика в серверный журнал.
    // Секрет webhook и содержимое запроса в журнал не выводим.
    error_log('Cadence webhook configuration failed: ' . get_class($e));

    // Сообщаем, что обработчик сейчас не может принять уведомление.
    http_response_code(503);

    // Без успешной проверки событие дальше не обрабатываем.
    exit;
}

// Второй блок: обрабатываем событие с уже проверенной подписью.
try {
    // Текущая версия интеграции принимает только тестовые события.
    if ($event->livemode !== false) {
        // Отклоняем события из реального режима.
        http_response_code(400);

        // Завершаем обработку без изменения заказа.
        exit('Test events only');
    }

    // Перечисляем события Checkout, которые поддерживает наш обработчик.
    $types = [
        // Checkout завершен; фактический статус оплаты проверит общая функция.
        'checkout.session.completed',

        // Отложенная оплата успешно подтверждена.
        'checkout.session.async_payment_succeeded',

        // Отложенная оплата завершилась ошибкой.
        'checkout.session.async_payment_failed',

        // Срок действия платежной сессии истек.
        'checkout.session.expired',
    ];

    // Обрабатываем только перечисленные типы событий.
    // true включает строгое сравнение значений.
    if (in_array($event->type, $types, true)) {
        // Получаем Checkout Session из события и преобразуем ее в PHP-массив.
        $session = $event->data->object->toArray();

        // Проверяем метку нашей интеграции в служебных данных платежа.
        // Сессии без этой метки пропускаем.
        if (
            ($session['metadata']['integration'] ?? '')
            === 'cadence_connections'
        ) {
            // Подключаемся к PostgreSQL для обновления заказа.
            $pdo = require __DIR__ . '/../config/db.php';

            // Передаем проверенное уведомление общей функции обработки платежа.
            // Она проверит заказ, сумму, валюту и идентификаторы.
            // Затем обновит статус, данные платежа и дату оплаты в транзакции.
            cadenceApplyPaymentSession(
                $pdo,
                $session,

                // Тип события нужен, в частности, для обработки ошибки оплаты.
                $event->type
            );
        }
    }

    // Подтверждаем успешную обработку запроса.
    // Для неподдерживаемых событий или чужой интеграции тоже возвращаем 200:
    // они намеренно пропущены, а не завершились технической ошибкой.
    http_response_code(200);

    // Возвращаем короткое тело ответа.
    // Этот ответ подтверждает прием запроса, а не обязательно статус paid.
    echo 'ok';

} catch (Throwable $e) {
    // Записываем тип ошибки обработки в серверный журнал.
    error_log('Cadence webhook processing failed: ' . get_class($e));

    // Сообщаем об ошибке, чтобы не подтверждать неудачную обработку.
    // Откат транзакции при ошибке выполняет cadenceApplyPaymentSession().
    http_response_code(500);
}