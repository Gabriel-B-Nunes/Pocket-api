<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\Request;
use App\service\Dispatcher;

$dispatcher = new Dispatcher();
$request = new Request();
$dispatcher->dispatch($request);