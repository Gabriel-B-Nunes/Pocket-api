<?php

namespace App\service\data\sanitizer;

use Attribute;


#[Attribute(Attribute::TARGET_PARAMETER)]
class ArraySanitizer implements SanitizerInterface {
    public function __construct(
        private bool $removeNull = true,
        private bool $removeEmptyString = true
    )
    {}

    public function sanitize(mixed $array): mixed
    {
        if (!is_array($array)) {
            return [];
        }

        $tmpArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $tmpArray[$key] = $this->sanitize($value);
                continue;
            }

            if ($value === null && $this->removeNull) {
                continue;
            }

            if ($value !== null && mb_trim((string) $value) === "" && $this->removeEmptyString) {
                continue;
            }

            $tmpArray[$key] = $value;
        }

        return $tmpArray;
    }
}
