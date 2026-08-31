<?php

namespace App\service\data\sanitizer;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)] 
class IntegerSanitizer implements SanitizerInterface {
    public function sanitize(mixed $value): mixed
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
}