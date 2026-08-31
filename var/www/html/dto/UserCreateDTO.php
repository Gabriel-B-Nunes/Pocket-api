<?php

declare(strict_types=1);

namespace App\dto;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\service\data\sanitizer\IntegerSanitizer;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\IntegerValidator;
use App\service\data\validator\StringValidator;

class UserCreateDTO {
    public function __construct(
        #[StringSanitizer(removeSpecialCharacters: true)]
        #[StringValidator(
            acceptNumbers: false, 
            acceptSpecialChars: false)]
        public readonly string $userName,

        #[StringSanitizer()]
        #[StringValidator(validEmail: true)]
        public readonly string $userEmail,

        #[StringSanitizer(
            removeBlank: true, 
            removeAlphanumerics: true, 
            removeSpecialCharacters: true)]
        #[StringValidator(
            minLength: 9,
            maxLength: 15,
            onlyAcceptNumbers: true)]
        public readonly string $userCellphoneNumber,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 12,
            maxLength: 64,
            mustContainNumbers: true,
            mustContainSpecialCharacters: true
        )]
        public readonly string $userPassword,

        #[IntegerSanitizer()]
        #[IntegerValidator(
            inArray: [0,1]
        )]
        public readonly int $userStatus
    )
    {}
}