<?php

namespace App\service;

use App\api\v1\group\CreateGroupController;
use App\api\v1\group\DeleteGroupController;
use App\api\v1\group\ReadGroupController;
use App\api\v1\group\UpdateGroupCotroller;
use App\api\v1\group\ReadUuidGroupController;
use App\api\v1\item\CreateItemController;
use App\api\v1\item\DeleteItemController;
use App\api\v1\item\ReadItemController;
use App\api\v1\item\ReadUuidItemController;
use App\api\v1\item\UpdateItemController;
use App\api\v1\item\UuidItemController;
use App\api\v1\movementItem\CreateMovementItemController;
use App\api\v1\user\CreateUserController;
use App\api\v1\user\LoginUserController;
use App\api\v1\user\RefreshUserController;
use App\exception\MethodNotAllowedException;
use App\exception\NotFoundException;
use App\model\Request;
use App\model\Response;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Dispatcher
{
    private array $routes = [
        "POST" => [
            //users
            "/^\/api\/v1\/users$/" => CreateUserController::class,
            "/^\/api\/v1\/users\/login$/" => LoginUserController::class,
            "/^\/api\/v1\/users\/refresh$/" => RefreshUserController::class,
            //groups
            "/^\/api\/v1\/groups$/" => CreateGroupController::class,
            //items
            "/^\/api\/v1\/items$/" => CreateItemController::class,
            //movementItems
            "/^\/api\/v1\/movementItems$/" => CreateMovementItemController::class
        ],
        "GET" => [
            //groups
            "/^\/api\/v1\/groups\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => ReadUuidGroupController::class,
            "/^\/api\/v1\/groups(?:(?:\?|\&)[\w\d\-]+\=[\w\d\-]+)*$/" => ReadGroupController::class,
            //items
            "/^\/api\/v1\/items\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => ReadUuidItemController::class,
            "/^\/api\/v1\/items(?:(?:\?|\&)[\w\d\-]+\=[\w\d\-]+)*$/" => ReadItemController::class
        ],
        "PUT" => [
            //groups
            "/^\/api\/v1\/groups\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => UpdateGroupCotroller::class,
            //items
            "/^\/api\/v1\/items\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => UpdateItemController::class
        ],
        "DELETE" => [
            //groups
            "/^\/api\/v1\/groups\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => DeleteGroupController::class,
            //items
            "/^\/api\/v1\/items\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => DeleteItemController::class
        ]
    ];

    public function dispatch(Request $request): Response
    {
        $url = $request->getUrl();
        $method = $request->getMethod();

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $class) {
                preg_match($route, $url, $matchs);

                if ($matchs) {
                    $controller = new $class();

                    return $controller->handleRequest($request);
                }
            }
            throw new NotFoundException();
        } else {
            throw new MethodNotAllowedException();
        }
    }
}
