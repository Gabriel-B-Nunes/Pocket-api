<?php

namespace App\service\translation;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

enum LangEnum {
    case PT_BR;
    case EN_US;

    public static function match (string $string) {
        return match($string) {
            "pt-BR", "pt" => self::PT_BR,
            "en-US", "en" => self::EN_US
        };
    }
}