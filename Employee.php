<?php
declare(strict_types=1);

class Employee
{
    private array $salaryHistory = [];

    public function __construct(
        public string $name,
        protected float $baseSalary,
        private string $passport,
    ) {}

    public function getSalary(): float
    {
        return $this->baseSalary;
    }

    public function getNetSalary(): float
    {
        return $this->getSalary() - $this->calculateTax($this->getSalary());
    }

    public function raiseSalary(float $percent): void
    {
        $this->validatePercent($percent);
        $old = $this->baseSalary;
        $this->baseSalary = round($old * (1 + $percent / 100), 2);
        $this->salaryHistory[] = sprintf('%.2f -> %.2f (+%s%%)', $old, $this->baseSalary, $percent);
    }

    // final: наследники не могут изменить правило маскировки паспорта
    final public function getMaskedPassport(): string
    {
        $digits = preg_replace('/\D/', '', $this->passport);
        return '**** **' . substr($digits, -4);
    }

    public function getSalaryHistory(): array
    {
        return $this->salaryHistory;
    }

    protected function calculateTax(float $amount): float
    {
        return round($amount * 0.13, 2);
    }

    private function validatePercent(float $p): void
    {
        if ($p < 1 || $p > 50) {
            throw new InvalidArgumentException("Процент повышения должен быть от 1 до 50, получено {$p}");
        }
    }
}

class Manager extends Employee
{
    private const BONUS_PER_SUBORDINATE = 5000;

    public function __construct(
        string $name,
        float $baseSalary,
        string $passport,
        private int $teamSize,
    ) {
        parent::__construct($name, $baseSalary, $passport);
    }

    public function getSalary(): float
    {
        return $this->baseSalary + self::BONUS_PER_SUBORDINATE * $this->teamSize;
    }

    // protected-оклад и protected-метод налога доступны в наследнике
    public function getBaseSalaryAfterTax(): float
    {
        return $this->baseSalary - $this->calculateTax($this->baseSalary);
    }
}

class Developer extends Employee
{
    private const LEVELS = ['junior' => 1.0, 'middle' => 1.5, 'senior' => 2.0];

    public function __construct(
        string $name,
        float $baseSalary,
        string $passport,
        private string $level,
    ) {
        if (!isset(self::LEVELS[$level])) {
            throw new InvalidArgumentException("Неизвестный уровень: {$level}");
        }
        parent::__construct($name, $baseSalary, $passport);
    }

    public function getSalary(): float
    {
        return $this->baseSalary * self::LEVELS[$this->level];
    }
}
