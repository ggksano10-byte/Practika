<?php
declare(strict_types=1);

namespace Shop\Helpers;

class PriceFormatter
{
    public static function format(float $price): string
    {
        // 1250 -> «1 250,00 ₽» (неразрывный пробел между тысячами и перед ₽)
        return number_format($price, 2, ',', "\u{00A0}") . "\u{00A0}₽";
    }
}
