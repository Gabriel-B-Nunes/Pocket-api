<?php
namespace App\service\data\validator;

interface ValidatorInterface {
    public function validate(mixed $value, string $name): bool;
    public function getErrorMessage(): string;
}