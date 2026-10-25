<?php

use App\service\controller\FrontController;

require_once __DIR__ . '/autoload.php';

$frontController = new FrontController();
$frontController->handle();
