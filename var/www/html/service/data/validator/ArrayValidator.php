<?php

namespace App\service\data\validator;

use App\service\cache\Container;
use Attribute;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

#[Attribute(Attribute::TARGET_PARAMETER)]
class ArrayValidator implements ValidatorInterface
{
    private array $errorMessages = [];

    public function __construct(
        private array $keysInArray = [],
        private bool $acceptEmpty = true,
    ) {}

    public function validate(mixed $value, string $name): bool
    {
        $validArray = true;
        $translator = Container::get("translator");

        if ($this->keysInArray) {
            foreach (array_keys($value) as $key) {
                if (!in_array($key, $this->keysInArray, true)) {
                    $implodedKeysInArray = implode(",", $this->keysInArray);
                    $this->errorMessages[] = $translator->translate(
                        sprintf("Field %s must be in array [%s]", $name, $implodedKeysInArray)
                    );
                    $validArray = false;
                }
            }
        }

        if (count($value) === 0 && !$this->acceptEmpty) {
            $this->errorMessages[] = $translator->translate(sprintf("Field %s cannot be empty", $name));
            $validArray = false;
        }

        return $validArray;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
