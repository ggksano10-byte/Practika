<?php
declare(strict_types=1);

require __DIR__ . '/Employee.php';

$staff = [
    new Employee('Анна Смирнова', 60000, '4512 673456'),
    new Manager('Игорь Волков', 90000, '4601 123789', 4),
    new Developer('Олег Ким', 80000, '4713 555012', 'junior'),
    new Developer('Мария Орлова', 80000, '4814 998877', 'middle'),
    new Developer('Денис Белов', 80000, '4915 000111', 'senior'),
];

echo "=== Сотрудники ===" . PHP_EOL;
foreach ($staff as $e) {
    printf(
        "%s | %s | до налога: %s ₽ | после налога: %s ₽ | паспорт: %s\n",
        $e->name,
        $e::class,
        number_format($e->getSalary(), 2, ',', ' '),
        number_format($e->getNetSalary(), 2, ',', ' '),
        $e->getMaskedPassport()
    );
}

echo PHP_EOL . "=== Повышение оклада ===" . PHP_EOL;
$staff[0]->raiseSalary(10);
$staff[0]->raiseSalary(5.5);
print_r($staff[0]->getSalaryHistory());
try {
    $staff[0]->raiseSalary(80);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== Эксперименты ===" . PHP_EOL;
$mgr = $staff[1];
$dev = $staff[2];

// Каждый эксперимент выполняется через замыкание; доступ из другого контекста
// проверяется Closure::bind (имитация «внутри Manager» / «внутри Developer»).
$experiments = [
    'Чтение $name снаружи' => fn() => $mgr->name,
    'Чтение $baseSalary снаружи' => fn() => $mgr->baseSalary,
    'Чтение $baseSalary в Manager' => fn() => $mgr->getBaseSalaryAfterTax() . ' (через метод Manager, чтение $this->baseSalary работает)',
    'Чтение $passport в Manager' => Closure::bind(fn() => $this->passport, $mgr, Manager::class),
    'Вызов calculateTax() снаружи' => fn() => $mgr->calculateTax(1000.0),
    'Вызов validatePercent() в Developer' => Closure::bind(fn() => $this->validatePercent(10.0), $dev, Developer::class),
];

// Обращение к чужому private-свойству родителя даёт Warning (свойство «невидимо»), делаем его исключением
set_error_handler(function (int $no, string $msg): never {
    throw new ErrorException("Warning: {$msg}");
});

foreach ($experiments as $label => $fn) {
    try {
        $result = $fn();
        echo "{$label}: OK -> " . var_export($result, true) . PHP_EOL;
    } catch (Error | ErrorException $e) {
        echo "{$label}: " . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    }
}

// Прямые попытки (закомментированы, т.к. приводят к фатальным ошибкам):
// echo $mgr->baseSalary;           // Error: Cannot access protected property Manager::$baseSalary
// echo $mgr->calculateTax(1000.0); // Error: Call to protected method Manager::calculateTax() from global scope
// class Dev2 extends Employee { public function getMaskedPassport(): string { return ''; } }
//   // Fatal error: Dev2::getMaskedPassport() cannot override final method Employee::getMaskedPassport()
