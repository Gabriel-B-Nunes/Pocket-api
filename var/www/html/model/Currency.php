<?php

namespace App\model;

use OpenApi\Attributes as OA;

#[OA\Schema(type: 'string', enum: ['USD', 'BRL'])]
enum Currency: string {
    case USD = "USD";
    case BRL = "BRL";
}