<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class InternalServerErrorException extends Exception {
    
    public function __construct(string $message = "The server encountered an error and could not complete your request.", int $code = 500)
    {
        return parent::__construct($message, $code);
    }
}