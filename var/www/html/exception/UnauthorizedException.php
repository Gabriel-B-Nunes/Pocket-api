<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class UnauthorizedException extends Exception {
    
    public function __construct(string $message = "Invalid email or password.", int $code = 401)
    {
        return parent::__construct($message, $code);
    }
}