<?php

namespace App\service\cache;

use Redis;


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
