<?php

declare(strict_types = 1);

namespace App\dto\transaction;

use App\service\data\sanitizer\IntegerSanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\IntegerValidator;
use App\service\data\validator\StringValidator;
use OpenApi\Attributes as OA;


#[OA\Schema]
class TransactionCreateDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(minLength: 36, maxLength: 36)]
        #[OA\Property]
        public readonly string $userUuid,

        #[StringSanitizer()]
        #[StringValidator(minLength: 36, maxLength: 36)]
        #[OA\Property]
        public readonly string $installmentUuid,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 10,
            maxLength: 10,
            validDateTime: true,
            dateTimeFormat: "Y-m-d"
        )]
        #[OA\Property]
        public readonly string $paymentDate,

        #[IntegerSanitizer()]
        #[IntegerValidator(acceptZero: false, min: 1)]
        #[OA\Property]
        public readonly int $paymentValue
    )
    {}
}
