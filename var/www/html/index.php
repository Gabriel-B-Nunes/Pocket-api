<?php

use App\service\handler\AuthenticationHandler;
use App\service\handler\DispatchingHandler;
use App\service\handler\ExceptionHandler;
use App\service\handler\TranslationHandler;
use App\service\Router;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

header('Content-Type: application/json');

$router = new Router();
$request = $router->resolve($_SERVER["REQUEST_METHOD"]);
$chain = new TranslationHandler(
    new ExceptionHandler(
        new AuthenticationHandler(
            new DispatchingHandler()
        )
    )
);

$chain->handle($request);
