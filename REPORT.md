# Практическая работа 2. Автозагрузка

## Порядок загрузки классов (собственный автозагрузчик)

```
--- старт скрипта, ни один класс ещё не загружен ---
Загружен класс: Shop\Models\Customer
Загружен класс: Shop\Services\Cart
Загружен класс: Shop\Models\Product
Покупатель: Иван Петров
Загружен класс: Shop\Helpers\PriceFormatter
Итого: 7 201,50 ₽
```

Классы загружаются лениво, в момент первого использования:

1. `Customer` — первая строка `new Customer(...)`.
2. `Cart` — `new Cart($customer)`.
3. `Product` — первый `new Product(...)` внутри `$cart->add(...)`.
4. `PriceFormatter` — вызов `PriceFormatter::format(...)` после расчёта итога.

До вызова `new` / статического вызова файл класса не подключается, хотя `use` в начале файла есть: `use` лишь задаёт псевдоним имени и ничего не загружает.

## Вариант с Composer

`composer.json` содержит `autoload.psr-4: {"Shop\\": "src/"}`, после `composer dump-autoload` скрипт `index_composer.php` подключает только `vendor/autoload.php` и работает так же, но без отладочного вывода.

## Ошибка при обращении к несуществующему классу

```
Error: Class "Shop\Models\Order" not found
```

(получена до создания файла `src/Models/Order.php`).

## Бонус: класс `Shop\Models\Order`

Файл `src/Models/Order.php` добавлен в уже существующую папку `Models/` — отдельная регистрация не нужна, потому что правило PSR-4 в `composer.json` задаёт соответствие «префикс `Shop\` → папка `src/`», а не список классов. Загрузчик сам вычисляет путь по имени класса: `Shop\Models\Order` → `src/Models/Order.php`.

Если же поменять сам префикс или папку в `composer.json` (например, `Shop\` → `App\`), Composer заранее сгенерировал карту префиксов в `vendor/composer/autoload_psr4.php`, и эта карта не обновится сама, поэтому нужно повторно выполнить `composer dump-autoload`.
