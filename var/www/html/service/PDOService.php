<?php

namespace App\service;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class PDOService
{
    private static ?\PDO $instance = null;

    private function __construct()
    {
        throw new \Exception('Not implemented');
    }

    private function __clone()
    {
        throw new \Exception('Not implemented');
    }

    public static function getInstance(): \PDO
    {
        if (empty(self::$instance)) {
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            try {
                self::$instance = new \PDO("mysql:host=db;dbname=pocket;charset=utf8mb4", "pocket", $_ENV["MYSQL_PASSWORD"], $options);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        
        return self::$instance;
    }
}
