<?php
declare(strict_types=1);

namespace Shop\Models;

class Product
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
    ) {}
}
