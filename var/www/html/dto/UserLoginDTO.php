<?php

declare(strict_types=1);

namespace App\dto;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\StringValidator;

class UserLoginDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(validEmail: true)]
        public readonly string $email,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 12,
            maxLength: 64,
            mustContainNumbers: true,
            mustContainSpecialCharacters: true
        )]
        public readonly string $password,
    )
    {}
}