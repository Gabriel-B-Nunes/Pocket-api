<?php

declare(strict_types = 1);

namespace App\dto\movement;

use App\dto\installment\InstallmentCreateDTO;
use App\dto\movementItem\MovementItemCreateDTO;
use App\model\FinancialType;
use App\model\Recurrence;
use App\service\data\sanitizer\ArraySanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\EnumValidator;
use App\service\data\validator\NestedDTOValidator;
use App\service\data\validator\StringValidator;


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
        #[NestedDTOValidator(
            MovementItemCreateDTO::class,
            injected: [
                'userUuid' => '00000000-0000-0000-0000-000000000000',
                'movementUuid' => '00000000-0000-0000-0000-000000000000'
            ]
        )]
        public readonly ?array $movementItemList = null,

        #[ArraySanitizer()]
        #[NestedDTOValidator(
            InstallmentCreateDTO::class,
            injected: [
                'userUuid' => '00000000-0000-0000-0000-000000000000',
                'movementUuid' => '00000000-0000-0000-0000-000000000000'
            ]
        )]
        public readonly ?array $installmentList = null
    )
    {}
}
