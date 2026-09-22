<?php

namespace App\service\translation;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

enum LangEnum {
    case PT_BR;
    case EN_US;

    public static function match(string $string): self {
        $normalized = strtolower(trim($string));
        $normalized = preg_replace('/\s*;.*$/', '', $normalized);
        $normalized = explode(',', $normalized)[0];

        return match ($normalized) {
            "pt", "pt-br" => self::PT_BR,
            "en", "en-us" => self::EN_US,
            default => self::EN_US,
        };
    }
}