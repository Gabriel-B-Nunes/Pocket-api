<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\EnumInterface;

enum MeasurementUnit: int
{
    case ALL = -1;
    case KG = 0;
    case LT = 1;
    case UN = 2;
    case MG = 3;
    case ML = 4;
    case MT = 5;
    case CM = 6;

    public function getPrecision(): int {
        return match($this) {
            self::KG, self::LT, self::MT => 3,
            self::MG, self::ML, self::CM => 0
        };
    }

    public function getFactor(): int {
        return 10 ** $this->getPrecision();
    }
}

class MeasurementUnitEnum implements EnumInterface
{
    public static function tryFrom(string|int $value)
    {
        return MeasurementUnit::tryFrom($value);
    }

    public static function cases()
    {
        return MeasurementUnit::cases();
    }

    public static function casesView()
    {
        return array_filter(MeasurementUnit::cases(), function ($case) {
            return $case->value !== -1;
        });
    }
}
