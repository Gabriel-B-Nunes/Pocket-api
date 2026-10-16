<?php

declare(strict_types=1);

namespace App\dto\user;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\Status;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\EnumValidator;
use App\service\data\validator\StringValidator;

class UserCreateDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(acceptSpecialChars: false)]
        public readonly string $name,

        #[StringSanitizer()]
        #[StringValidator(validEmail: true)]
        public readonly string $email,

        #[StringSanitizer(
            removeBlank: true, 
            removeAlphanumerics: true, 
            removeSpecialCharacters: true)]
        #[StringValidator(
            minLength: 9,
            maxLength: 15,
            onlyAcceptNumbers: true)]
        public readonly string $cellphoneNumber,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 12,
            maxLength: 64,
            mustContainNumbers: true,
            mustContainSpecialCharacters: true
        )]
        public readonly string $password,

        #[StringSanitizer()]
        #[EnumValidator(Status::class)]
        public readonly string $status
    )
    {}
}