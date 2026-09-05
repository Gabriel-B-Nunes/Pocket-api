<?php

namespace App\service;

use App\api\UserController;
use App\model\Request;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Dispatcher {
    private array $routes = [
        "user" => UserController::class
    ];

    public function dispatch(Request $request) {
        $baseModule = $request->getModules()[2] ?? null;

        if (array_key_exists($baseModule, $this->routes)) {
            $controllerClass = $this->routes[$baseModule];
            $controller = new $controllerClass();

            $controller->handleRequest($request);
        } else {
            throw new \Exception("The requested URL was not found on this server", 404);
        }
    }
}