<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class UniqueConstraintViolationException extends Exception {
    
    public function __construct(string $message = "The request could not be completed due to a conflict with the current state of the resource.", int $code = 409)
    {
        return parent::__construct($message, $code);
    }
}