<?php

declare(strict_types=1);

namespace App\dto\user;


use App\model\Status;
use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\EnumValidator;
use App\service\data\validator\StringValidator;
use OpenApi\Attributes as OA;

#[OA\Schema]
class UserCreateDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator(acceptSpecialChars: false)]
        #[OA\Property]
        public readonly string $name,

        #[StringSanitizer()]
        #[StringValidator(validEmail: true)]
        #[OA\Property]
        public readonly string $email,

        #[StringSanitizer(
            removeBlank: true, 
            removeAlphanumerics: true, 
            removeSpecialCharacters: true)]
        #[StringValidator(
            minLength: 9,
            maxLength: 15,
            onlyAcceptNumbers: true)]
        #[OA\Property]
        public readonly string $cellphoneNumber,

        #[StringSanitizer()]
        #[StringValidator(
            minLength: 12,
            maxLength: 64,
            mustContainNumbers: true,
            mustContainSpecialCharacters: true
        )]
        #[OA\Property]
        public readonly string $password,

        #[StringSanitizer()]
        #[EnumValidator(Status::class)]
        #[OA\Property(ref: Status::class)]
        public readonly string $status
    )
    {}
}