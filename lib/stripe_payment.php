<?php

// Рассчитывает подпись состава корзины.
// Принимает массив товаров и возвращает хеш в виде строки.
function cadenceCartSignature(array $cart): string
{
    // Здесь соберем данные, определяющие состав корзины.
    $parts = [];

    // Последовательно обрабатываем каждую позицию.
    foreach ($cart as $item) {
        // Для сравнения используем идентификатор товара, количество и вариант.
        // Название, изображение и цена в эту подпись не входят.
        $parts[] = [
            // Идентификатор товара; при отсутствии используем пустую строку.
            (string) ($item['slug'] ?? ''),

            // Количество приводим к целому числу.
            (int) ($item['quantity'] ?? 0),

            // Вариант приводим к строке и удаляем пробелы по краям.
            trim((string) ($item['product_option'] ?? '')),
        ];
    }

    // Сортируем позиции для стабильного порядка данных.
    // Перестановка одинаковых позиций не должна менять подпись.
    sort($parts);

    // Преобразуем состав корзины в JSON и вычисляем его хеш.
    return hash(
        'sha256',
        json_encode(
            $parts,

            // При ошибке преобразования выбрасываем исключение.
            // Кириллицу сохраняем без Unicode-экранирования.
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
        )
    );
}

// Проверяет соответствие Checkout Session сохраненному заказу.
// При несовпадении выбрасывает исключение; при успехе ничего не возвращает.
function cadenceValidatePaymentSession(array $order, array $session): void
{
    // Получаем служебные данные, прикрепленные к сессии Stripe.
    $metadata = $session['metadata'] ?? [];

    // Проверяем среду, тип платежа, связь с заказом, валюту и сумму.
    if (
        // Принимаем только явно указанный тестовый режим.
        ($session['livemode'] ?? true) !== false

        // Ожидаем разовую оплату, а не подписку или сохранение карты.
        || ($session['mode'] ?? '') !== 'payment'

        // Платеж должен принадлежать нашей интеграции.
        || ($metadata['integration'] ?? '') !== 'cadence_connections'

        // Номер заказа в metadata должен совпадать с записью в базе.
        || (string) ($metadata['order_id'] ?? '') !== (string) $order['id']

        // Дополнительная ссылка Stripe на заказ тоже должна совпадать.
        || (string) ($session['client_reference_id'] ?? '') !== (string) $order['id']

        // Проверяем связь платежа с токеном исходной формы оформления.
        || !hash_equals(
            (string) $order['checkout_token'],
            (string) ($metadata['checkout_token'] ?? '')
        )

        // Идентификатор должен относиться к тестовой Checkout Session.
        || !str_starts_with((string) ($session['id'] ?? ''), 'cs_test_')

        // Валюта Stripe должна совпадать с валютой заказа.
        || strtolower((string) ($session['currency'] ?? '')) !== $order['payment_currency']

        // Итоговая сумма Stripe должна совпадать с ожидаемой суммой в копейках.
        || (int) ($session['amount_total'] ?? -1) !== (int) $order['payment_amount_minor']

        // Если заказ уже связан с сессией, не принимаем другую сессию.
        || (
            !empty($order['stripe_session_id'])
            && $order['stripe_session_id'] !== $session['id']
        )
    ) {
        // Прерываем обновление заказа при любом несовпадении.
        throw new UnexpectedValueException(
            'Checkout session does not match the saved order.'
        );
    }
}

// Применяет подтвержденные данные Stripe к заказу в PostgreSQL.
// Передавать сюда только ответ Stripe API или уведомление с проверенной подписью.
// Сама эта функция не проверяет HTTP-подпись: это делает stripe_webhook.php.
//
// $pdo — подключение к базе.
// $session — данные Checkout Session.
// $eventType — тип webhook-события; при проверке через API может быть пустым.
// Возвращает обновленный заказ либо null для другой интеграции.
function cadenceApplyPaymentSession(
    PDO $pdo,
    array $session,
    string $eventType = ''
): ?array {
    // Пропускаем сессии, которые не имеют метки нашего магазина.
    if (($session['metadata']['integration'] ?? '') !== 'cadence_connections') {
        return null;
    }

    // Получаем номер заказа из metadata и проверяем его как целое число.
    $id = filter_var(
        $session['metadata']['order_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    // Номер заказа должен быть положительным целым числом.
    if ($id === false || $id < 1) {
        throw new UnexpectedValueException('Invalid order reference.');
    }

    // Начинаем транзакцию обновления платежа.
    $pdo->beginTransaction();

    try {
        // Получаем заказ и блокируем его строку до завершения транзакции.
        // Конкурирующие обновления этой строки будут ждать.
        $get = $pdo->prepare(
            'SELECT * FROM orders WHERE id = :id FOR UPDATE'
        );

        // Передаем номер заказа отдельно от текста SQL.
        $get->execute(['id' => $id]);

        // Получаем запись заказа либо false, если она не найдена.
        $order = $get->fetch();

        // Не создаем заказ по входящему уведомлению: он должен уже существовать.
        if (!$order) {
            throw new RuntimeException('Order not found.');
        }

        // Проверяем, что платеж действительно соответствует этому заказу.
        cadenceValidatePaymentSession($order, $session);

        // По умолчанию сохраняем текущий статус заказа.
        $status = $order['payment_status'];

        // Статус paid в Stripe означает подтвержденную оплату.
        if (($session['payment_status'] ?? '') === 'paid') {
            $status = 'paid';

        // Истекшую сессию отмечаем только у еще не оплаченного заказа.
        } elseif (
            $status !== 'paid'
            && ($session['status'] ?? '') === 'expired'
        ) {
            $status = 'expired';

        // Ошибка отложенной оплаты также не должна сбросить уже сохраненный paid.
        } elseif (
            $status !== 'paid'
            && $eventType === 'checkout.session.async_payment_failed'
        ) {
            $status = 'failed';
        }

        // Получаем PaymentIntent: обычно это строковый идентификатор платежа.
        // Если его пока нет, используем null.
        $intent = $session['payment_intent'] ?? null;

        // Если Stripe вернул развернутый объект в виде массива, извлекаем его id.
        if (is_array($intent)) {
            $intent = $intent['id'] ?? null;
        }

        // Подготавливаем обновление платежных данных заказа.
        // COALESCE для PaymentIntent сохраняет прежний id, если новый равен null.
        // Дата оплаты устанавливается только для paid.
        // COALESCE для paid_at сохраняет дату первого подтверждения оплаты.
        $update = $pdo->prepare(
            'UPDATE orders
             SET stripe_session_id = :session_id,
                 stripe_payment_intent_id =
                     COALESCE(:intent, stripe_payment_intent_id),
                 payment_status = :status,
                 paid_at = CASE
                     WHEN :is_paid = 1
                     THEN COALESCE(paid_at, CURRENT_TIMESTAMP)
                     ELSE paid_at
                 END
             WHERE id = :id'
        );

        // Передаем проверенные значения в запрос обновления.
        $update->execute([
            // Идентификатор Checkout Session.
            'session_id' => $session['id'],

            // Идентификатор PaymentIntent либо null.
            'intent' => $intent,

            // Рассчитанный статус заказа.
            'status' => $status,

            // Числовой флаг для условия установки даты оплаты в SQL.
            'is_paid' => $status === 'paid' ? 1 : 0,

            // Идентификатор обновляемого заказа.
            'id' => $id,
        ]);

        // Повторно читаем заказ после обновления.
        $get->execute(['id' => $id]);

        // Получаем актуальный статус, идентификаторы и дату оплаты.
        $order = $get->fetch();

        // Фиксируем изменения и освобождаем блокировку строки.
        $pdo->commit();

        // Возвращаем обновленную запись вызывающему обработчику.
        return $order;

    } catch (Throwable $e) {
        // Если транзакция еще активна, отменяем ее изменения.
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // Передаем ошибку дальше.
        // Webhook вернет ошибочный HTTP-ответ, а страница результата покажет сообщение.
        throw $e;
    }
}

// Завершает работу с корзиной после подтвержденной оплаты.
// Вызывается на странице результата, где открыта PHP-сессия покупателя.
// Ничего не возвращает: изменяет данные текущей сессии.
function cadenceFinishCart(array $order): void
{
    // Не очищаем корзину для ожидающих, ошибочных или истекших платежей.
    if ($order['payment_status'] === 'paid') {
        // Запоминаем номер последнего подтвержденного заказа.
        $_SESSION['last_order_id'] = $order['id'];

        // Сравниваем текущую корзину с составом оплаченного заказа.
        // Если покупатель изменил корзину во время оплаты, подписи различаются.
        if (
            cadenceCartSignature($_SESSION['cart'] ?? [])
            === $order['cart_signature']
        ) {
            // Удаляем корзину из сессии только при совпадении состава.
            unset($_SESSION['cart']);
        }
    }
}