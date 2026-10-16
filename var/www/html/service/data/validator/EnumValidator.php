<?php

namespace App\service\data\validator;

use App\exception\InternalServerErrorException;
use App\service\cache\Container;
use Attribute;
use ReflectionEnum;
use ReflectionException;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

#[Attribute(Attribute::TARGET_PARAMETER)]
class EnumValidator implements ValidatorInterface
{
    private array $errorMessages = [];

    public function __construct(
        private string $enum
    )
    {}

    public function validate(mixed $value, string $name): bool
    {
        if (!enum_exists($this->enum)) {
            throw new InternalServerErrorException();
        }

        $validEnum = true;
        $enum = new ReflectionEnum($this->enum);
        $translator = Container::get("translator");

        try {
            $enum->getCase($value);
        } catch (ReflectionException $e) {
            $implodedCases = implode(",", array_map(function ($value) {
                return $value->name;
            }, $enum->getCases()));
            $this->errorMessages[] = sprintf($translator->translate("Field %s must be in array [%s]"), $name, $implodedCases);
            $validEnum = false;
        }

        return $validEnum;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
