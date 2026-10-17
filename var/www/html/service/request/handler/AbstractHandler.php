<?php

namespace App\service\request\handler;

use App\service\request\handler\HandlerInterface;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

abstract class AbstractHandler implements HandlerInterface {
    private AbstractHandler $nextHandler;

    public function setNext(AbstractHandler $handler): void
    {
        $this->nextHandler = $handler;
    }
}