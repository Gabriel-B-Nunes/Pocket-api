<?php

namespace App\service\data\validator;

use App\service\cache\Container;
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
        $translator = Container::get("translator");

        if ($this->min && $value < $this->min) {
            $this->errorMessages[] = sprintf($translator->translate("Field %s cannot be smaller than %d."), $name, $this->min);
            $validInteger = false;
        }

        if ($this->max && $value > $this->max) {
            $this->errorMessages[] = sprintf($translator->translate("Field %s cannot be greater than %d."), $name, $this->max);
            $validInteger = false;
        }

        if ($value === null && !$this->nullable) {
            $this->errorMessages[] = sprintf($translator->translate("Field %s cannot be null."), $name);
            $validInteger = false;
        }

        if ($value == 0 && !$this->acceptZero) {
            $this->errorMessages[] = sprintf($translator->translate("Field %s cannot be zero."), $name);
            $validInteger = false;
        }

        if ($this->inArray && !in_array($value, $this->inArray)) {
            $implodedArray = implode(",", $this->inArray);
            $this->errorMessages[] = sprintf($translator->translate("Field %s must be in array [%s]."), $name, $implodedArray);
            $validInteger = false;
        }

        return $validInteger;
    }

    public function getErrorMessages(): array {
        return $this->errorMessages;
    }
}
