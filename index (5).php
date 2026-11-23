<?php
declare(strict_types=1);

require __DIR__ . '/QueryBuilder.php';

echo "=== Запросы ===" . PHP_EOL;

echo QueryBuilder::table('users')
    ->select('id', 'name', 'email')
    ->where('age', '>=', 18)
    ->where('city', '=', 'Москва')
    ->orderBy('name')
    ->limit(10) . PHP_EOL;

echo QueryBuilder::table('products') . PHP_EOL;

echo QueryBuilder::table('products')
    ->select('title', 'price')
    ->where('title', 'LIKE', 'Ноут%')
    ->orderBy('price', 'desc')
    ->limit(5)
    ->toSql() . PHP_EOL;

echo QueryBuilder::table('orders')
    ->where('status', '!=', 'cancelled')
    ->where('total', '>', 1000) . PHP_EOL;

echo PHP_EOL . "=== Бонус: __call ===" . PHP_EOL;
echo QueryBuilder::table('users')->whereCity('Москва')->whereAge(30)->whereFirstName('Анна') . PHP_EOL;

echo PHP_EOL . "=== Ошибки ===" . PHP_EOL;
$bad = [
    'направление' => fn() => QueryBuilder::table('t')->orderBy('id', 'UP'),
    'limit = 0'   => fn() => QueryBuilder::table('t')->limit(0),
    'оператор'    => fn() => QueryBuilder::table('t')->where('a', '<>', 1),
    'метод'       => fn() => QueryBuilder::table('t')->foo(),
];
foreach ($bad as $label => $fn) {
    try {
        $fn();
    } catch (InvalidArgumentException | BadMethodCallException $e) {
        echo "[{$label}] " . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . "=== Экранирование кавычек ===" . PHP_EOL;
echo QueryBuilder::table('users')->where('name', '=', "O'Brien") . PHP_EOL;
