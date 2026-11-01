<?php

declare(strict_types = 1);

namespace App\dto\installment;

use App\service\data\sanitizer\ArraySanitizer;
use App\service\data\sanitizer\IntegerSanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\IntegerValidator;
use App\service\data\validator\StringValidator;


class InstallmentCreateDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(minLength: 36, maxLength: 36)]
        public readonly string $userUuid,

        #[StringSanitizer()]
        #[StringValidator(minLength: 36, maxLength: 36)]
        public readonly string $movementUuid,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 10,
            maxLength: 10,
            validDateTime: true,
            dateTimeFormat: "Y-m-d"
        )]
        public readonly string $dueDate,

        #[IntegerSanitizer()]
        #[IntegerValidator(acceptZero: false, min: 1)]
        public readonly int $dueValue,

        #[ArraySanitizer()]
        public readonly ?array $transactionList = null
    )
    {}
}
