<?php
declare(strict_types=1);

class QueryBuilder
{
    private const OPERATORS = ['=', '!=', '>', '<', '>=', '<=', 'LIKE'];

    private array $columns = ['*'];
    private array $conditions = [];
    private ?string $orderBy = null;
    private ?int $limit = null;

    private function __construct(private string $table) {}

    public static function table(string $name): static
    {
        return new static($name);
    }

    public function select(string ...$columns): static
    {
        $this->columns = $columns === [] ? ['*'] : $columns;
        return $this;
    }

    public function where(string $column, string $operator, string|int $value): static
    {
        $operator = strtoupper($operator);
        if (!in_array($operator, self::OPERATORS, true)) {
            throw new InvalidArgumentException("Недопустимый оператор: {$operator}");
        }
        $this->conditions[] = "{$column} {$operator} " . $this->quote($value);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            throw new InvalidArgumentException("Направление сортировки должно быть ASC или DESC, получено: {$direction}");
        }
        $this->orderBy = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $count): static
    {
        if ($count <= 0) {
            throw new InvalidArgumentException("Limit должен быть больше 0, получено: {$count}");
        }
        $this->limit = $count;
        return $this;
    }

    public function toSql(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . " FROM {$this->table}";
        if ($this->conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }
        if ($this->orderBy !== null) {
            $sql .= " ORDER BY {$this->orderBy}";
        }
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        return $sql;
    }

    public function __toString(): string
    {
        return $this->toSql();
    }

    // Бонус: whereCity('Москва') -> where('city', '=', 'Москва')
    public function __call(string $name, array $arguments): static
    {
        if (str_starts_with($name, 'where') && strlen($name) > 5) {
            $column = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', substr($name, 5)));
            if (!array_key_exists(0, $arguments)) {
                throw new InvalidArgumentException("Метод {$name}() требует значение");
            }
            return $this->where($column, '=', $arguments[0]);
        }
        throw new BadMethodCallException("Метод {$name}() не существует");
    }

    private function quote(string|int $value): string
    {
        if (is_int($value)) {
            return (string) $value;
        }
        return "'" . str_replace("'", "''", $value) . "'";
    }
}
