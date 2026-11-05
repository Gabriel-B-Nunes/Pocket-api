<?php

declare(strict_types = 1);

namespace App\dto\installment;

use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\StringValidator;
use OpenApi\Attributes as OA;


#[OA\Schema]
class InstallmentUpdateDTO
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
        #[OA\Property]
        public readonly ?string $dueDate,

        #[StringSanitizer()]
        #[StringValidator(
            acceptZero: false,
            min: 1,
            nullable: true
        )]
        #[OA\Property]
        public readonly ?int $dueValue
    )
    {
    }
}
