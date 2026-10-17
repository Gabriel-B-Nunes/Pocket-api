<?php

use App\service\controller\FrontController;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

$frontController = new FrontController();
$frontController->handle();