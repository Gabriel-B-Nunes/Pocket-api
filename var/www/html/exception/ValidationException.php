<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class ValidationException extends Exception {

    public function __construct(string $message = "Validation failed for the provided payload.", int $code = 422, private array $errors = [])
    {
        parent::__construct($message, $code);
    }

    public function getErrors(): array {
        return $this->errors;
    }
}