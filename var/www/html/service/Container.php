<?php

namespace App\service;

class Container {
    private static array $instances;

    public function __construct()
    {
        throw new \Exception('Not implemented');
    }

    public function __clone()
    {
        throw new \Exception('Not implemented');
    }

    public static function set(string $name, object $object): void {
        self::$instances[$name] = $object;
    }

    public static function get(string $name): ?object {
        return self::$instances[$name] ?? null;
    }
}