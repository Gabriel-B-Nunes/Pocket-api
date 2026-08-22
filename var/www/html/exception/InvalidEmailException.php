<?php
namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Throwable;

class InvalidEmailException extends \Exception {
    public function __construct(string $value, int $code = 0, Throwable|null $previous = null)
    {
        $message = _("The email provided is invalid." . $value . ".");
        return parent::__construct($message, $code, $previous);
    }
}