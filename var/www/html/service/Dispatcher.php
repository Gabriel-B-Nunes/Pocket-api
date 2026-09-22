<?php

namespace App\service;

use App\api\user\CreateUserController;
use App\api\user\LoginUserController;
use App\api\user\RefreshUserController;
use App\exception\NotFoundException;
use App\model\Request;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Dispatcher {
    private array $routes = [
        "/api/user/create" => CreateUserController::class,
        "/api/user/login" => LoginUserController::class,
        "/api/user/refresh" => RefreshUserController::class
    ];

    public function dispatch(Request $request): void {
        $uri = $request->getUrl();

        if (array_key_exists($uri, $this->routes)) {
            $controllerClass = $this->routes[$uri];
            $controller = new $controllerClass();

            $controller->handleRequest($request);
        } else {
            throw new NotFoundException();
        }
    }
}