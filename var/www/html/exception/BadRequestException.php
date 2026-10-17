<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class BadRequestException extends Exception {
    
    public function __construct(
        string $message = "The request payload is malformed or missing required parameters.", int $code = 400)
    {
        return parent::__construct($message, $code);
    }
}