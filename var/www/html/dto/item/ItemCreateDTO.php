<?php

declare(strict_types = 1);

namespace App\dto\item;

use App\model\Status;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\EnumValidator;
use App\service\data\validator\StringValidator;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class ItemCreateDTO {
    public function __construct(
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
        public readonly string $groupUuid,
        
        #[StringSanitizer()]
        #[StringValidator(
            acceptNumbers: false,
            acceptSpecialChars: false)]
        public readonly string $name,

        #[StringSanitizer()]
        #[EnumValidator(Status::class)]
        public readonly string $status
    )
    {}
}