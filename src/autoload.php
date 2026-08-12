<?php
spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', '/', $class);
    
    $file = __DIR__ . '/' . preg_replace('/^App\//', '', $classPath) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
