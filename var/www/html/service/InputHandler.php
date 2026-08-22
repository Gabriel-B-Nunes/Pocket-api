<?php

namespace App\service;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\exception\InvalidAlphanumericException;
use App\exception\InvalidEmailException;
use App\exception\InvalidCellphoneNumberException;
use App\exception\InvalidEnumException;
use App\exception\InvalidIntegerException;
use App\exception\InvalidDatetimeException;
use App\exception\InvalidBooleanException;
use App\exception\InvalidStringLengthException;
use App\model\EnumInterface;

class InputHandler
{
    public static function alphanumericValidator(?string $alphanumericString, bool $nullable = false): ?string
    {
        if ($nullable == true and $alphanumericString === null) {
            return null;
        }

        $sanitized = filter_var($alphanumericString, FILTER_CALLBACK, ["options" => [self::class, "alphanumericSanitizer"]]);

        $options = function ($value) {
            return (bool) preg_match('/^[a-zA-Zá-úÁ-Ú\s]+$/', $value);
        };

        if ($sanitized !== "" && filter_var($sanitized, FILTER_CALLBACK, ["options" => $options])) {
            return $sanitized;
        }

        throw new InvalidAlphanumericException((string) $sanitized);
    }

    private static function alphanumericSanitizer(?string $string): ?string
    {
        return preg_replace("/[^a-zA-Zá-úÁ-Ú\s]/", "", trim(strip_tags($string ?? '')));
    }

    public static function emailValidator(?string $email, bool $nullable = false): ?string
    {
        if ($nullable == true and $email === null) {
            return null;
        }

        $sanitized = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
            return $sanitized;
        }

        throw new InvalidEmailException((string) $sanitized);
    }

    public static function cellphoneNumberValidator(?string $cellphoneNumber, bool $nullable = false): ?string
    {
        if ($nullable == true and $cellphoneNumber === null) {
            return null;
        }

        $sanitized = filter_var($cellphoneNumber, FILTER_SANITIZE_NUMBER_INT);

        if (filter_var($sanitized, FILTER_VALIDATE_INT)) {
            return $sanitized;
        }

        throw new InvalidCellphoneNumberException((string) $sanitized);
    }

    public static function enumValidator(?int $status, EnumInterface $enum, bool $nullable = false): ?int
    {
        if ($nullable == true and $status === null) {
            return null;
        }

        $sanitized = filter_var($status, FILTER_SANITIZE_NUMBER_INT);
        $statusFound = in_array($sanitized, $enum->notAllCases());

        if (isset($statusFound) && $enum::tryFrom($sanitized)) {
            return $sanitized;
        }

        throw new InvalidEnumException((string) $sanitized);
    }

    public static function integerValidator(?int $integer, bool $nullable = false): ?int
    {
        if ($nullable == true and $integer === null) {
            return null;
        }

        $sanitized = filter_var($integer, FILTER_SANITIZE_NUMBER_INT);

        if ($sanitized !== null && $sanitized !== "") {
            return $sanitized;
        }

        throw new InvalidIntegerException((string) $sanitized);
    }

    public static function datetimeValidator(?string $datetime, ?string $format, bool $nullable = false): ?string
    {
        if ($nullable == true && $datetime === null) {
            return null;
        }

        $sanitized = \DateTime::createFromFormat("Y-m-d H:i:s", $datetime) ?: \DateTime::createFromFormat("Y-m-d\TH:i", $datetime) ?: \DateTime::createFromFormat("Y-m-d", $datetime);
        $errors = \DateTime::getLastErrors();

        if ($sanitized instanceof \DateTimeInterface && ($errors['warning_count'] ?? 0) === 0 && ($errors['error_count'] ?? 0) === 0) {
            return $sanitized->format($format);
        }

        throw new InvalidDatetimeException((string) $datetime);
    }

    public static function booleanValidator(string|int|null $boolean, bool $nullable = false): ?int
    {
        if ($nullable == true and $boolean === null) {
            return null;
        }

        $sanitized = filter_var((int) $boolean, FILTER_SANITIZE_NUMBER_INT);
        $validated = filter_var($sanitized, FILTER_VALIDATE_INT);

        if ($validated !== false && in_array($validated, [0, 1], true)) {
            return $validated;
        }

        throw new InvalidBooleanException((string) $boolean);
    }

    public static function stringLengthValidator(?string $string, array $options = [], bool $nullable = false): ?string
    {
        if ($nullable && $string === null) {
            return null;
        }

        $length = mb_strlen((string)$string, "UTF-8");

        if (isset($options["min"]) && $length < $options["min"]) {
            throw new InvalidStringLengthException($string);
        }

        if (isset($options["max"]) && $length > $options["max"]) {
            throw new InvalidStringLengthException($string);
        }

        return $string;
    }
}
