<?php

namespace App\service\data;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use ReflectionClass;

class InputHandler {
    private array $errorMessages = [];

    public function handle(string $dtoClass, array $rawData): object|bool {
        $reflection = new ReflectionClass($dtoClass);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $dtoClass();
        }

        $processedData = [];

        foreach ($constructor->getParameters() as $parameter) {
            $fieldName = $parameter->getName();

            $value = $rawData[$fieldName] ?? null;

            $attributes = $parameter->getAttributes();
            
            foreach ($attributes as $attribute) {
                $attributeReflection = $attribute->newInstance();

                if (method_exists($attributeReflection, "sanitize")) {
                    $value = $attributeReflection->sanitize($value);
                }
            }

            foreach ($attributes as $attribute) {
                $attributeReflection = $attribute->newInstance();

                if (method_exists($attributeReflection, "validate")) {
                    if (!$attributeReflection->validate($value, $fieldName)) {
                        $this->errorMessages[$fieldName] = $attributeReflection->getErrorMessages();
                    }
                }
            }

            $processedData[$fieldName] = $value;
        }

        if ($this->errorMessages) {
            return false;
        }

        return $reflection->newInstanceArgs($processedData);
    }

    public function getErrorMessages(): array {
        return $this->errorMessages;
    }
}