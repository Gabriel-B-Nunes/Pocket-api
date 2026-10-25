<?php

declare(strict_types = 1);

namespace App\dto\movementItem;

use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\StringValidator;


class MovementItemDeleteDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(
            minLength: 36,
            maxLength: 36
        )]
        public readonly string $uuid,
        
        #[StringSanitizer()]
        #[StringValidator(
            minLength: 36,
            maxLength: 36
        )]
        public readonly string $userUuid
    )
    {}
}