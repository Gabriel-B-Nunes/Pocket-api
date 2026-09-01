<?php

namespace App\service\data\validator;

use App\service\data\validator\ValidatorInterface;
use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)] 
class IntegerValidator implements ValidatorInterface
{
    public function __construct(
        private array $errorMessages = [],
        private ?int $min = null,
        private ?int $max = null,
        private bool $nullable = false,
        private bool $acceptZero = true,
        private array $inArray = []
    )
    {}

    public function validate(mixed $value, string $name): bool
    {
        $validInteger = true;

        if ($this->min && $value < $this->min) {
            $this->errorMessages[] = "Field {$name} cannot be smaller than {$this->min}.";
            $validInteger = false;
        }

        if ($this->max && $value > $this->max) {
            $this->errorMessages[] = "Field {$name} cannot be greater than {$this->max}.";
            $validInteger = false;
        }

        if ($value === null && !$this->nullable) {
            $this->errorMessages[] = "Field {$name} cannot be null.";
            $validInteger = false;
        }

        if ($value == 0 && !$this->acceptZero) {
            $this->errorMessages[] = "Field {$name} cannot be zero.";
            $validInteger = false;
        }

        if ($this->inArray && !in_array($value, $this->inArray)) {
            $implodedArray = implode(",", $this->inArray);
            $this->errorMessages[] = "Field {$name} must be in array [{$implodedArray}].";
            $validInteger = false;
        }

        return $validInteger;
    }

    public function getErrorMessages(): array {
        return $this->errorMessages;
    }
}
