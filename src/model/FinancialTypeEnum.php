<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\EnumInterface;

enum FinancialType: int
{
    case ALL = -1;
    case REVENUE = 0;
    case EXPENSE = 1;
    case BOTH = 2;
}

class FinancialTypeEnum implements EnumInterface
{
    public static function tryFrom(string|int $value): ?FinancialType
    {
        return FinancialType::tryFrom($value);
    }

    public static function cases()
    {
        return FinancialType::cases();
    }

    public static function casesView()
    {
        return array_filter(FinancialType::cases(), function ($case) {
            return $case->value !== -1;
        });
    }

    public static function casesMovement()
    {
        return array_filter(FinancialType::cases(), function ($case) {
            return in_array($case->value, [0,1]);
        });
    }
}
