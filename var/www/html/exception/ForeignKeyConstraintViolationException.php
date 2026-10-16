<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class ForeignKeyConstraintViolationException extends Exception {
    
    public function __construct(string $message = "The resource could not be deleted/updated because it is being used by another entity.", int $code = 409, private string $error = "")
    {
        return parent::__construct($message, $code);
    }

    public function getError(): string {
        return $this->error;
    }
}