<?php
namespace App\service\data\sanitizer;

interface SanitizerInterface {
    public function sanitize(mixed $value): mixed;
}