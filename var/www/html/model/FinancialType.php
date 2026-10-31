<?php

declare(strict_types=1);

namespace App\model;

enum FinancialType: string
{
    case CREDIT = "CREDIT";
    case DEBIT = "DEBIT";
}
