# Практическая работа 5. Модификаторы доступа

## Таблица экспериментов

| Действие | Где выполняется | Результат |
|---|---|---|
| Чтение `$name` | снаружи | Работает: `public`, вернулось «Игорь Волков» |
| Чтение `$baseSalary` | снаружи | `Error: Cannot access protected property Manager::$baseSalary` |
| Чтение `$baseSalary` | в Manager | Работает: `protected` доступен наследнику |
| Чтение `$passport` | в Manager | `Warning: Undefined property: Manager::$passport` — `private` родителя для наследника «невидим» |
| Вызов `calculateTax()` | снаружи | `Error: Call to protected method Employee::calculateTax() from global scope` |
| Вызов `validatePercent()` | в Developer | `Error: Call to private method Employee::validatePercent() from scope Developer` |

## Переопределение final-метода

```
Fatal error: Cannot override final method Employee::getMaskedPassport()
```

## Почему оклад `protected`, а не `public`

- Наследникам (`Manager`, `Developer`) оклад нужен для расчёта итоговой зарплаты, поэтому `private` не подходит.
- Внешнему коду прямой доступ не нужен: читать зарплату можно через `getSalary()`, менять — только через `raiseSalary()`.
- Так сохраняется контроль: процент повышения проверяется (1–50 %), каждое изменение попадает в историю.

## Чем рискует программа, если все свойства `public`

- Любой код сможет записать оклад напрямую (`$e->baseSalary = -1`) в обход проверок и истории изменений.
- Паспортные данные станут доступны всем, а не только классу `Employee`.
- Внутреннее устройство класса превратится в часть публичного интерфейса: любое изменение (например, переименование свойства) сломает весь код, который его использует.
- Объект может оказаться в некорректном состоянии, а искать источник ошибки придётся по всей программе.
