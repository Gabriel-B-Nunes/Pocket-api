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

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class MovementItemUpdateDTO {
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
        public readonly string $userUuid,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 36,
            maxLength: 36
        )]
        public readonly string $itemUuid,

        #[IntegerSanitizer()]
        #[IntegerValidator(
            min: 1
        )]
        public readonly int $unitValue,

        #[IntegerSanitizer()]
        #[IntegerValidator(
            min: 1
        )]
        public readonly int $quantity,

        #[StringSanitizer()]
        #[EnumValidator(Unity::class)]
        public readonly string $unity,

        #[StringSanitizer()]
        #[EnumValidator(Currency::class)]
        public readonly string $currency
    )
    {}
}