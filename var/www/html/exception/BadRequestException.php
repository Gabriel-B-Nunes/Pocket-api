<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class BadRequestException extends Exception {
    
    public function __construct(string $message = "The request payload is malformed or missing required parameters.", int $code = 400, private string $error = "")
    {
        return parent::__construct($message, $code);
    }

    public function getError(): string {
        return $this->error;
    }
}