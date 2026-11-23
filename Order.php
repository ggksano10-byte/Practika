<?php
declare(strict_types=1);

namespace Shop\Models;

class Order
{
    public function __construct(
        public readonly int $id,
        public readonly Customer $customer,
    ) {}
}
