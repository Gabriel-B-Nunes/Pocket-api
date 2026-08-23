<?php

declare(strict_types=1);

namespace App\dto;

class UserCreateDTO {
    public function __construct(
        public readonly string $userName,
        public readonly string $userEmail,
        public readonly string $userCellphoneNumber,
        public readonly string $userPassword,
        public readonly int $userStatus
    )
    {}
}