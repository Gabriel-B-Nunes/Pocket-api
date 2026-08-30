<?php

namespace App\service\data\sanitizer;

class IntegerSanitizer implements SanitizerInterface {
    public function sanitize(mixed $value): mixed
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
}