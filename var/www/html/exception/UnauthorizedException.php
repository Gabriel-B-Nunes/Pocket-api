<?php

namespace App\exception;


use Exception;

class UnauthorizedException extends Exception {
    
    public function __construct(string $message = "Invalid email or password.", int $code = 401)
    {
        return parent::__construct($message, $code);
    }
}