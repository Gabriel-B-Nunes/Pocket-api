<?php

declare(strict_types=1);

namespace App\model;

use OpenApi\Attributes as OA;

#[OA\Schema(type: 'string', enum: ['CREDIT', 'DEBIT'])]
enum FinancialType: string
{
    case CREDIT = "CREDIT";
    case DEBIT = "DEBIT";
}
