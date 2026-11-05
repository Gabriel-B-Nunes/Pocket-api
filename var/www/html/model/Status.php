<?php

namespace App\model;

use OpenApi\Attributes as OA;

#[OA\Schema(type: 'string', enum: ['ACTIVE', 'DISABLED'])]
enum Status: string {
    case ACTIVE = "ACTIVE";
    case DISABLED = "DISABLED";
}