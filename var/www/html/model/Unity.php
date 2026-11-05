<?php

namespace App\model;

use OpenApi\Attributes as OA;

#[OA\Schema(type: 'string', enum: ['UN', 'KG', 'MG', 'LT', 'ML', 'M', 'CM'])]
enum Unity: string {
    case UN = "UN";
    case KG = "KG";
    case MG = "MG";
    case LT = "LT";
    case ML = "ML";
    case M = "M";
    case CM = "CM";
}