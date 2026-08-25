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
        $baseModule = $request->getModules()[2];

        if (array_key_exists($baseModule, $this->routes)) {
            print_r("route found");
            $controllerClass = $this->routes[$baseModule];
            $controller = new $controllerClass();

            $controller->handleRequest($request);
        }
    }
}