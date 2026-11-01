<?php

declare(strict_types = 1);

namespace App\dto\movement;

use App\service\data\sanitizer\ArraySanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\EnumValidator;
use App\service\data\validator\StringValidator;
use App\model\FinancialType;
use App\model\Recurrence;


class MovementCreateDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(minLength: 36, maxLength: 36)]
        public readonly string $userUuid,

        #[StringSanitizer()]
        #[StringValidator(minLength: 1, maxLength: 255)]
        public readonly string $description,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 10,
            maxLength: 10,
            validDateTime: true,
            dateTimeFormat: "Y-m-d"
        )]
        public readonly string $transactionDate,

        #[StringSanitizer()]
        #[EnumValidator(FinancialType::class)]
        public readonly string $financialType,

        #[StringSanitizer()]
        #[EnumValidator(Recurrence::class, nullable: true)]
        public readonly ?string $recurrence = null,

        #[ArraySanitizer()]
        public readonly ?array $movementItemList = null,

        #[ArraySanitizer()]
        public readonly ?array $installmentList = null
    )
    {}
}
