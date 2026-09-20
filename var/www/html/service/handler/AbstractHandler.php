<?php

namespace App\service\handler;

use App\service\HandlerInterface;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

abstract class AbstractHandler implements HandlerInterface {
    private AbstractHandler $nextHandler;
}