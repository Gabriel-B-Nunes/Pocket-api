<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\EnumInterface;

enum Status: int
{
    case ALL = -1;
    case ACTIVE = 0;
    case BLOCKED = 1;
}

class StatusEnum implements EnumInterface
{
    public static function tryFrom(string|int $value): ?Status
    {
        return Status::tryFrom($value);
    }

    public static function cases()
    {
        return Status::cases();
    }

    public static function notAllCases()
    {
        return array_filter(Status::cases(), function ($case) {
            return $case->value !== -1;
        });
    }
}
