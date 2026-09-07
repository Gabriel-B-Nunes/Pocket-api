<?php

namespace App\service;

use Redis;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class RedisService
{
    private static ?Redis $instance = null;

    private function __construct()
    {
        throw new \Exception('Not implemented');
    }

    private function __clone()
    {
        throw new \Exception('Not implemented');
    }

    public static function getInstance(): Redis
    {
        if (empty(self::$instance)) {
            self::$instance = new Redis();
            self::$instance->connect("redis", 6379);
            return self::$instance;
        }
        
        return self::$instance;
    }
}
