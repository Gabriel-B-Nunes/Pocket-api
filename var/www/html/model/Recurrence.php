<?php

declare(strict_types=1);

namespace App\model;

use OpenApi\Attributes as OA;

#[OA\Schema(type: 'string', enum: ['DAILY', 'WEEKLY', 'MONTHLY', 'ANNUALLY'])]
enum Recurrence: string
{
    case DAILY = "DAILY";
    case WEEKLY = "WEEKLY";
    case MONTHLY = "MONTHLY";
    case ANNUALLY = "ANNUALLY";
}
