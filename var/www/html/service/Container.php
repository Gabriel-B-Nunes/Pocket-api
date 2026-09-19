<?php

namespace App\service;

class Container {
    private static array $instances;

    public static function set(string $name, object $object): void {
        self::$instances[$name] = $object;
    }

    public static function get(string $name): ?object {
        return self::$instances[$name] ?? null;
    }
}