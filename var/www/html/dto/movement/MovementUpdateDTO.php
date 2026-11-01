<?php

declare(strict_types = 1);

namespace App\dto\movement;

use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\StringValidator;


class MovementUpdateDTO
{
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(
            minLength: 1,
            maxLength: 255,
            nullable: true
        )]
        public readonly ?string $description,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 10,
            maxLength: 10,
            validDateTime: true,
            dateTimeFormat: "Y-m-d",
            nullable: true
        )]
        public readonly ?string $transactionDate,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 1,
            maxLength: 10,
            nullable: true
        )]
        public readonly ?string $financialType,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 1,
            maxLength: 10,
            nullable: true
        )]
        public readonly ?string $recurrence
    )
    {
    }
}
