<?php

declare(strict_types=1);

namespace App\dto;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class UserRefreshDTO {
    public function __construct(
        public readonly string $refreshToken,
    )
    {}
}