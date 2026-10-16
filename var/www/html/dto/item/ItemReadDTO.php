<?php

declare(strict_types=1);

namespace App\dto\item;

use App\service\data\sanitizer\ArraySanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\ArrayValidator;
use App\service\data\validator\StringValidator;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class ItemReadDTO
{
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(
            minLength: 36,
            maxLength: 36
        )]
        public readonly string $userUuid,

        #[ArraySanitizer()]
        #[ArrayValidator(
            keysInArray: [
                "name",
                "status",
                "createdAt",
                "createdAt_begin",
                "createdAt_end",
                "updatedAt",
                "updatedAt_begin",
                "updatedAt_end"
            ]
        )]
        public readonly array $queryString
    ) {}
}
