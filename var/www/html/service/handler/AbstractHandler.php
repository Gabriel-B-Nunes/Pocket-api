<?php

namespace App\service\handler;

<<<<<<< HEAD
use App\service\handler\HandlerInterface;
=======
use App\service\HandlerInterface;
>>>>>>> refactor-request

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

abstract class AbstractHandler implements HandlerInterface {
    private AbstractHandler $nextHandler;
}