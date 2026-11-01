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
use App\api\v1\movementItem\CreateMovementItemController;
use App\api\v1\movementItem\DeleteMovementItemController;
use App\api\v1\movementItem\ReadMovementItemCotroller;
use App\api\v1\movementItem\ReadUuidMovementItemController;
use App\api\v1\movementItem\UpdateMovementItemController;
use App\api\v1\movement\CreateMovementController;
use App\api\v1\movement\ReadUuidMovementController;
use App\api\v1\installment\CreateInstallmentController;
use App\api\v1\installment\DeleteInstallmentController;
use App\api\v1\installment\ReadInstallmentController;
use App\api\v1\installment\ReadUuidInstallmentController;
use App\api\v1\installment\UpdateInstallmentController;
use App\api\v1\movement\DeleteMovementController;
use App\api\v1\movement\ReadMovementController;
use App\api\v1\movement\UpdateMovementController;
use App\api\v1\transaction\CreateTransactionController;
use App\api\v1\transaction\DeleteTransactionController;
use App\api\v1\transaction\ReadUuidTransactionController;
use App\api\v1\transaction\UpdateTransactionController;
use App\api\v1\user\CreateUserController;
use App\api\v1\user\VerifyUserController;
use App\api\v1\user\LoginUserController;
use App\api\v1\user\RefreshUserController;
use App\dto\transaction\TransactionReadDTO;
use App\exception\MethodNotAllowedException;
use App\exception\NotFoundException;
use App\model\Request;
use App\model\Response;


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
            //movements
            "/^\/api\/v1\/movements$/" => CreateMovementController::class,
            //movementItems
            "/^\/api\/v1\/movementItems$/" => CreateMovementItemController::class,
            //installments
            "/^\/api\/v1\/installments$/" => CreateInstallmentController::class,
            //transactions
            "/^\/api\/v1\/transactions$/" => CreateTransactionController::class
        ],
        "GET" => [
            //users
            "/^\/api\/v1\/users\/verify\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => VerifyUserController::class,
            //groups
            "/^\/api\/v1\/groups\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => ReadUuidGroupController::class,
            "/^\/api\/v1\/groups(?:(?:\?|\&)[\w\d\-]+\=[\w\d\-]+)*$/" => ReadGroupController::class,
            //items
            "/^\/api\/v1\/items\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => ReadUuidItemController::class,
            "/^\/api\/v1\/items(?:(?:\?|\&)[\w\d\-]+\=[\w\d\-]+)*$/" => ReadItemController::class,
            //movements
            "/^\/api\/v1\/movements\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => ReadUuidMovementController::class,
            "/^\/api\/v1\/movements(?:(?:\?|\&)[\w\d\-]+\=[\w\d\-]+)*$/" => ReadMovementController::class,
            //movementItems
            "/^\/api\/v1\/movementItems\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => ReadUuidMovementItemController::class,
            "/^\/api\/v1\/movementItems(?:(?:\?|\&)[\w\d\-]+\=[\w\d\-]+)*$/" => ReadMovementItemCotroller::class,
            //installments
            "/^\/api\/v1\/installments\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => ReadUuidInstallmentController::class,
            "/^\/api\/v1\/installments(?:(?:\?|\&)[\w\d\-]+\=[\w\d\-]+)*$/" => ReadInstallmentController::class,
            //transactions
            "/^\/api\/v1\/transactions\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => ReadUuidTransactionController::class,
            "/^\/api\/v1\/transactions(?:(?:\?|\&)[\w\d\-]+\=[\w\d\-]+)*$/" => TransactionReadDTO::class,
        ],
        "PUT" => [
            //groups
            "/^\/api\/v1\/groups\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => UpdateGroupCotroller::class,
            //items
            "/^\/api\/v1\/items\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => UpdateItemController::class,
            //movements
            "/^/api/v1/movements/uuid/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$" => UpdateMovementController::class,
            //movementItems
            "/^\/api\/v1\/movementItems\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => UpdateMovementItemController::class,
            //installments
            "/^/api/v1/installments/uuid/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$" => UpdateInstallmentController::class,
            //transactions
            "/^/api/v1/transactions/uuid/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$" => UpdateTransactionController::class,
        ],
        "DELETE" => [
            //groups
            "/^\/api\/v1\/groups\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => DeleteGroupController::class,
            //items
            "/^\/api\/v1\/items\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => DeleteItemController::class,
            //movements
            "/^/api/v1/movements/uuid/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$" => DeleteMovementController::class,
            //movementItems
            "/^\/api\/v1\/movementItems\/uuid\/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$/" => DeleteMovementItemController::class,
            //installments
            "/^/api/v1/installments/uuid/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$" => DeleteInstallmentController::class,
            //transactions
            "/^/api/v1/transactions/uuid/[\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12}$" => DeleteTransactionController::class,
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
