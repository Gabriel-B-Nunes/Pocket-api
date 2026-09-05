<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class UniqueConstraintViolationException extends Exception {
    
    public function __construct(string $message = "Duplicate key value violates unique constraint.", int $code = 400)
    {
        return parent::__construct($message, $code);
    }
}