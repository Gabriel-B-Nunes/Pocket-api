<?php

declare(strict_types = 1);

namespace App\service\data\validator;

use App\service\cache\Container;
use App\service\data\InputHandler;
use App\service\translation\Translator;
use Attribute;


#[Attribute(Attribute::TARGET_PARAMETER)]
class NestedDTOValidator implements ValidatorInterface
{
    private array $errorMessages = [];

    public function __construct(
        private string $dtoClass,
        private array $injected = []
    )
    {}

    public function validate(mixed $value, string $name): bool
    {
        if (!is_array($value)) {
            return true;
        }

        $valid = true;

        if (Container::get("translator") === null) {
            Container::set("translator", new Translator());
        }

        foreach ($value as $index => $item) {
            if ($item instanceof $this->dtoClass) {
                continue;
            }

            if (!is_array($item)) {
                $valid = false;
                $this->errorMessages[] = sprintf("Field %s[%s] must be an array.", $name, $index);
                continue;
            }

            $data = array_merge($this->injected, $item);
            $handler = new InputHandler();
            try {
                $dto = $handler->handle($this->dtoClass, $data);
            } catch (\Throwable $e) {
                $valid = false;
                $this->errorMessages[] = sprintf("Field %s[%s]: %s", $name, $index, $e->getMessage());
                continue;
            }

            if ($dto === false) {
                $valid = false;
                foreach ($handler->getErrorMessages() as $field => $messages) {
                    foreach ((array) $messages as $message) {
                        $this->errorMessages[] = sprintf("Field %s[%s].%s: %s", $name, $index, $field, $message);
                    }
                }
            }
        }

        return $valid;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
