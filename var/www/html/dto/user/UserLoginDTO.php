<?php

declare(strict_types=1);

namespace App\dto\user;


use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\StringValidator;
use OpenApi\Attributes as OA;

#[OA\Schema]
class UserLoginDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(validEmail: true)]
        #[OA\Property]
        public readonly string $email,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 12,
            maxLength: 64,
            mustContainNumbers: true,
            mustContainSpecialCharacters: true
        )]
        #[OA\Property]
        public readonly string $password,
    )
    {}
}