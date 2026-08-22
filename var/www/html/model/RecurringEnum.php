<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\EnumInterface;

enum Recurring: int
{
    case ALL = -1;
    case NO = 0;
    case YES = 1;
}

class RecurringEnum implements EnumInterface
{
    public static function tryFrom(string|int $value): ?Recurring
    {
        return Recurring::tryFrom($value);
    }

    public static function cases()
    {
        return Recurring::cases();
    }

    public static function casesView()
    {
        return array_filter(Recurring::cases(), function ($case) {
            return $case->value !== -1;
        });
    }
}
