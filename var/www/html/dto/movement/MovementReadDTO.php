<?php

declare(strict_types = 1);

namespace App\dto\movement;

use App\service\data\sanitizer\ArraySanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\ArrayValidator;
use App\service\data\validator\StringValidator;
use OpenApi\Attributes as OA;


#[OA\Schema]
class MovementReadDTO
{
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
                "description",
                "transactionDate",
                "transactionDate_begin",
                "transactionDate_end",
                "financialType",
                "recurrence",
                "createdAt",
                "createdAt_begin",
                "createdAt_end",
                "updatedAt",
                "updatedAt_begin",
                "updatedAt_end",
                "page"
            ]
        )]
        #[OA\Property]
        public readonly array $queryString
    )
    {
    }
}