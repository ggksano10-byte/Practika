<?php
declare(strict_types=1);

require __DIR__ . '/Book.php';
require __DIR__ . '/Library.php';

$b1 = new Book('Мастер и Маргарита', 'М. Булгаков', 1967, 480);
$lib = new Library();
$lib->addBook($b1);
$lib->addBook(new Book('Собачье сердце', 'М. Булгаков', 1987, 160));
$lib->addBook(new Book('Преступление и наказание', 'Ф. Достоевский', 1866, 672));
$lib->addBook(new Book('Война и мир', 'Л. Толстой', 1869, 1300));
$lib->addBook(new Book('Анна Каренина', 'Л. Толстой', 1877, 864));

echo "=== Все книги ===" . PHP_EOL;
$lib->printAll();

echo PHP_EOL . "=== Книги М. Булгакова ===" . PHP_EOL;
foreach ($lib->findByAuthor('М. Булгаков') as $book) {
    echo $book->getInfo() . PHP_EOL;
}

echo PHP_EOL . "Количество книг: " . $lib->count() . PHP_EOL;
echo "Возраст «{$b1->title}»: " . $b1->getAge() . " лет" . PHP_EOL;

$oldest = $lib->getOldest();
echo "Самая старая: " . ($oldest?->getInfo() ?? 'библиотека пуста') . PHP_EOL;
echo "Пустая библиотека: " . ((new Library())->getOldest()?->getInfo() ?? 'null') . PHP_EOL;

echo PHP_EOL . "=== Присваивание и клонирование ===" . PHP_EOL;
$b2 = $b1;        // вторая ссылка на тот же объект
$b3 = clone $b1;  // независимая копия
$b1->title = 'ИЗМЕНЕНО';

echo '$b1: ' . $b1->getInfo() . PHP_EOL;
echo '$b2: ' . $b2->getInfo() . "  (изменился вместе с \$b1)" . PHP_EOL;
echo '$b3: ' . $b3->getInfo() . "  (не изменился)" . PHP_EOL;
