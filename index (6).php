<?php
declare(strict_types=1);

require __DIR__ . '/Student.php';

$students = [];
$data = [
    [1001, 'Иванов', 'Пётр', 1, 'ivanov@mail.ru', [5, 4, 5, 5]],
    [1002, 'Петрова', 'Анна', 2, null, [4, 4, 3, 5]],
    [1003, 'Сидоров', 'Олег', 3, 'sidorov@mail.ru', [3, 4, 4]],
    [1004, 'Кузнецова', 'Мария', 4, 'kuz@mail.ru', [5, 5, 5, 4, 5]],
];

foreach ($data as [$id, $last, $first, $course, $email, $grades]) {
    $s = new Student($id, $last, $first, $course);
    $s->email = $email;
    $s->grades = $grades;
    $students[] = $s;
}

// printf считает байты, а не символы, поэтому для кириллицы дополняем пробелами сами
function pad(string $text, int $width): string
{
    return $text . str_repeat(' ', max(0, $width - mb_strlen($text)));
}

echo "Университет: " . Student::$university . PHP_EOL;
echo pad('Билет', 6) . ' | ' . pad('ФИО', 18) . ' | ' . pad('Курс', 4) . ' | ' . pad('Почта', 16) . ' | Ср. балл' . PHP_EOL;
echo str_repeat('-', 68) . PHP_EOL;
foreach ($students as $s) {
    echo pad((string) $s->id, 6) . ' | '
        . pad($s->getFullName(), 18) . ' | '
        . pad((string) $s->course, 4) . ' | '
        . pad($s->email ?? 'не указана', 16) . ' | '
        . number_format($s->getAverage(), 2) . PHP_EOL;
}
echo PHP_EOL . 'Всего студентов: ' . Student::$totalStudents . PHP_EOL;

echo PHP_EOL . "=== Эксперименты ===" . PHP_EOL;
$s = $students[0];

echo "1) Изменение readonly \$id:" . PHP_EOL;
try {
    $s->id = 5;
} catch (Error $e) {
    echo '   ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
}

echo "2) Строка в \$course при strict_types=1:" . PHP_EOL;
try {
    $s->course = 'два';
} catch (TypeError $e) {
    echo '   ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
}

echo "3) Студент с курсом 7:" . PHP_EOL;
try {
    new Student(9999, 'Тестов', 'Тест', 7);
} catch (InvalidArgumentException $e) {
    echo '   ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
}

echo "4) Изменение Student::\$university:" . PHP_EOL;
Student::$university = 'МГУ';
echo '   Student::$university = ' . Student::$university
    . ' (статическое свойство одно на класс — изменилось для всех объектов)' . PHP_EOL;
echo '   Студент с курсом 7 не создан, счётчик остался: ' . Student::$totalStudents . PHP_EOL;

echo "5) var_dump() и get_object_vars() (вне класса видны только public):" . PHP_EOL;
var_dump($s);
print_r(get_object_vars($s));

echo "Бонус: __set()" . PHP_EOL;
$s->nickname = 'Петя';
print_r($s->getHistory());
