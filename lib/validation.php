<?php

// Проверяем целые числа без зависимости от расширения PHP filter.
function cadenceValidateInt(mixed $value): int|false
{
    if (is_int($value)) {
        return $value;
    }

    if (!is_string($value)) {
        return false;
    }

    $value = trim($value);

    if (!preg_match('/\A[+-]?(?:0|[1-9][0-9]*)\z/', $value)) {
        return false;
    }

    $normalized = ltrim($value, '+');

    if ($normalized === '-0') {
        $normalized = '0';
    }

    $number = (int) $value;

    // Обнаруживаем переполнение: слишком большое число нельзя принять.
    return (string) $number === $normalized ? $number : false;
}

function is_valid_russian_phone_number($phone): bool
{
    if (!is_string($phone) || strlen($phone) > 40) {
        return false;
    }

    $phone = trim($phone);
    $phone = preg_replace('/[ ()-]/', '', $phone);

    return preg_match('/\A(?:\+?7|8)[0-9]{10}\z/', $phone) === 1;
}