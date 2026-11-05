<?php

declare(strict_types = 1);

namespace App\dto\installment;

use App\service\data\sanitizer\ArraySanitizer;
use App\service\data\sanitizer\IntegerSanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\IntegerValidator;
use App\service\data\validator\StringValidator;
use OpenApi\Attributes as OA;


#[OA\Schema]
class InstallmentCreateDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(minLength: 36, maxLength: 36)]
        #[OA\Property]
        public readonly string $userUuid,

        #[StringSanitizer()]
        #[StringValidator(minLength: 36, maxLength: 36)]
        #[OA\Property]
        public readonly string $movementUuid,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 10,
            maxLength: 10,
            validDateTime: true,
            dateTimeFormat: "Y-m-d"
        )]
        #[OA\Property]
        public readonly string $dueDate,

        #[IntegerSanitizer()]
        #[IntegerValidator(acceptZero: false, min: 1)]
        #[OA\Property]
        public readonly int $dueValue,

        #[ArraySanitizer()]
        #[OA\Property]
        public readonly ?array $transactionList = null
    )
    {}
}
