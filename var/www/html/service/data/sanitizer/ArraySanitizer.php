<?php

namespace App\service\data\sanitizer;

use Attribute;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

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

        $tmpArray = (array) $array;

        foreach ($tmpArray as $key => $value) {
            if ($value === null && $this->removeNull) {
                unset($tmpArray[$key]);
                continue;
            }

            if ($value !== null && mb_trim($value) === "" && $this->removeEmptyString) {
                unset($tmpArray[$key]);
            } 
        }

        return $tmpArray;
    }
}