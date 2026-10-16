<?php

declare(strict_types = 1);

namespace App\dto\group;

use App\service\data\sanitizer\ArraySanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\ArrayValidator;
use App\service\data\validator\StringValidator;

class GroupReadDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(
            minLength: 36,
            maxLength: 36
        )]
        public readonly string $userUuid,

        #[ArraySanitizer()]
        #[ArrayValidator(
            keysInArray: ["name", 
            "status", 
            "createdAt", 
            "createdAt_begin", 
            "createdAt_end", 
            "updatedAt", 
            "updatedAt_begin", 
            "updatedAt_end",
            "page"]
        )]
        public readonly array $queryString
    )
    {}
}