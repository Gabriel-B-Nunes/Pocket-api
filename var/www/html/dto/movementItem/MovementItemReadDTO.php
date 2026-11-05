<?php

declare(strict_types = 1);

namespace App\dto\movementItem;

use App\service\data\sanitizer\ArraySanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\ArrayValidator;
use App\service\data\validator\StringValidator;
use OpenApi\Attributes as OA;


#[OA\Schema]
class MovementItemReadDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(
            minLength: 36,
            maxLength: 36
        )]
        #[OA\Property]
        public readonly string $userUuid,

        #[ArraySanitizer()]
        #[ArrayValidator(
            keysInArray: [
                "itemUuid",
                "unitValue",
                "quantity",
                "unity",
                "currency",
                "createdAt",
                "createdAt_begin",
                "createdAt_end",
                "updatedAt",
                "updatedAt_begin",
                "updatedAt_end"
            ]
        )]
        #[OA\Property]
        public readonly array $queryString
    )
    {}
}