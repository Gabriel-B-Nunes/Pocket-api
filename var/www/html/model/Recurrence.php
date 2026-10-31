<?php

declare(strict_types=1);

namespace App\model;

enum Recurrence: string
{
    case DAILY = "DAILY";
    case WEEKLY = "WEEKLY";
    case MONTHLY = "MONTHLY";
    case ANNUALLY = "ANNUALLY";
}
