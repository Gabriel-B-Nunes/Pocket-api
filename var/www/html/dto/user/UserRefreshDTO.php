<?php

declare(strict_types=1);

namespace App\dto\user;

use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\StringValidator;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class UserRefreshDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator()]
        public readonly string $refreshToken,
    )
    {}
}