<?php

namespace App\service\data\validator;

use App\service\data\validator\ValidatorInterface;

class IntegerValidator implements ValidatorInterface
{
    private string $errorMessage = "";
    private bool $nullable;
    private bool $acceptZero;

    public function __construct(array|int $options = 0)
    {
        $this->nullable = $options["NULLABLE"] ?? false;
        $this->acceptZero = $options["ACCEPT_ZERO"] ?? true;
    }

    public function validate(mixed $value, string $name): bool
    {
        if ($value === null && $this->nullable) {
            return true;
        }

        if ($value === null && !$this->nullable) {
            $this->errorMessage = "Field {$name} cannot be null.";
            return false;
        }

        if ($value == 0 && !$this->acceptZero) {
            $this->errorMessage = "Field {$name} cannot be zero.";
            return false;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return true;
        }

        $this->errorMessage = "Field {$name} must be a valid integer.";
        return false;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}
