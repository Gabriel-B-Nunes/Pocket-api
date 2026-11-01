<?php

declare(strict_types = 1);

namespace App\dto\transaction;

use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\StringValidator;


class TransactionUpdateDTO
{
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(
            minLength: 10,
            maxLength: 10,
            validDateTime: true,
            dateTimeFormat: "Y-m-d",
            nullable: true
        )]
        public readonly ?string $paymentDate,

        #[StringSanitizer()]
        #[StringValidator(
            acceptZero: false,
            min: 1,
            nullable: true
        )]
        public readonly ?int $paymentValue
    )
    {
    }
}
