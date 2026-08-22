<?php
namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class ModelFactory {
    public static function buildGetObject(string $namespace, array $variables): ModelInterface {
        return $namespace::getConstructor($variables);
    }

    public static function buildPostObject(string $namespace, array $variables): ModelInterface {
        return $namespace::postConstructor($variables);
    }
}