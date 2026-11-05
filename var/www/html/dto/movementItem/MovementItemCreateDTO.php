<?php

declare(strict_types = 1);

namespace App\dto\movementItem;

use App\model\Currency;
use App\model\Unity;
use App\service\data\sanitizer\IntegerSanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\EnumValidator;
use App\service\data\validator\IntegerValidator;
use App\service\data\validator\StringValidator;
use OpenApi\Attributes as OA;


#[OA\Schema]
class MovementItemCreateDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(
            minLength: 36,
            maxLength: 36
        )]
        #[OA\Property]
        public readonly string $userUuid,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 36,
            maxLength: 36
        )]
        #[OA\Property]
        public readonly string $movementUuid,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 36,
            maxLength: 36
        )]
        #[OA\Property]
        public readonly string $itemUuid,

        #[IntegerSanitizer()]
        #[IntegerValidator(
            acceptZero: false,
            min: 1)]
        #[OA\Property]
        public readonly int $unitValue,

        #[IntegerSanitizer()]
        #[IntegerValidator(
            acceptZero: false,
            min: 1)]
        #[OA\Property]
        public readonly int $quantity,

        #[StringSanitizer()]
        #[EnumValidator(Unity::class)]
        #[OA\Property(ref: Unity::class)]
        public readonly string $unity,

        #[StringValidator()]
        #[EnumValidator(Currency::class)]
        #[OA\Property(ref: Currency::class)]
        public readonly string $currency
    )
    {}
}
