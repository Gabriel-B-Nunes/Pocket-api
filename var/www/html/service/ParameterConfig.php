<?php

namespace App\service;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\dao\ParameterDAO;

class ParameterConfig
{
    private static ?ParameterConfig $instance = null;
    private ParameterDAO $parameterDao;
    private static array $config = [];
    private static string $cacheLocation;

    private function __construct()
    {
        $this->parameterDao = new ParameterDAO();
        self::$cacheLocation = $_SERVER["DOCUMENT_ROOT"] . "/tmp/parameter_cache.php";
        $this->loadConfig();
    }

    private function __clone()
    {
        throw new \Exception('Not implemented');
    }

    public function __wakeup()
    {
        throw new \Exception('Not implemented');
    }

    public static function getInstance(): ParameterConfig
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function loadConfig(): void
    {
        if (file_exists(self::$cacheLocation)) {
            self::$config = include self::$cacheLocation;
            return;
        }

        $dir = dirname(self::$cacheLocation);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        self::$config = $this->parameterDao->readAll();

        $content = "<?php\nreturn " . var_export(self::$config, true) . ";\n";
        file_put_contents(self::$cacheLocation, $content);
    }

    public static function getParameterValue(string $name): ?string
    {
        self::getInstance();
        return self::$config[$name] ?? null;
    }

    public static function cleanCache(): void
    {
        if (file_exists(self::$cacheLocation)) {
            unlink(self::$cacheLocation);
        }

        if (function_exists("opcache_invalidate")) {
            @opcache_invalidate(self::$cacheLocation, true);
        }
    }
}
