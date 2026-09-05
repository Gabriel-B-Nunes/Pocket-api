<?php

namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Exception;

class NotFoundException extends Exception {
    
    public function __construct(string $message = "The requested URL was not found on this server.", int $code = 404)
    {
        return parent::__construct($message, $code);
    }
}