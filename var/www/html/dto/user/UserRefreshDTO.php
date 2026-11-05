<?php

declare(strict_types=1);

namespace App\dto\user;

use App\service\data\sanitizer\StringSanitizer;
use App\service\data\validator\StringValidator;
use OpenApi\Attributes as OA;


#[OA\Schema]
class UserRefreshDTO {
    public function __construct(
        #[StringSanitizer()]
        #[StringValidator()]
        #[OA\Property]
        public readonly string $refreshToken,
    )
    {}
}