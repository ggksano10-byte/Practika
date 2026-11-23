<?php
declare(strict_types=1);

// Вариант 1: собственный автозагрузчик
require __DIR__ . '/autoload.php';

use Shop\Helpers\PriceFormatter;
use Shop\Models\Customer;
use Shop\Models\Product;
use Shop\Services\Cart;

echo "--- старт скрипта, ни один класс ещё не загружен ---" . PHP_EOL;

$customer = new Customer('Иван Петров', 'ivan@example.com');
$cart = new Cart($customer);
$cart->add(new Product('Клавиатура', 3500.0), 1);
$cart->add(new Product('Мышь', 1250.0), 2);
$cart->add(new Product('Коврик', 400.5), 3);

echo 'Покупатель: ' . $cart->getCustomer()->name . PHP_EOL;
echo 'Итого: ' . PriceFormatter::format($cart->getTotal()) . PHP_EOL;

// Проверка ошибки: несуществующий класс
try {
    new Shop\Models\Order(1, $customer);
} catch (Error $e) {
    echo get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
}
