<?php
namespace App\exception;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use Throwable;

class InvalidCellphoneNumberException extends \Exception {
    public function __construct(string $value, int $code = 0, Throwable|null $previous = null)
    {
        $message = _("The cellphone number provided is invalid:" . $value . ".");
        return parent::__construct($message, $code, $previous);
    }
}