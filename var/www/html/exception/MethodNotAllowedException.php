<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class MethodNotAllowedException extends Exception {
    
    public function __construct(string $message = "The request method is not allowed.", int $code = 405, private string $error = "")
    {
        return parent::__construct($message, $code);
    }

    public function getError(): string {
        return $this->error;
    }
}