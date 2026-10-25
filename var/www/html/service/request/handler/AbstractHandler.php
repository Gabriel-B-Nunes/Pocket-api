<?php

namespace App\service\request\handler;

use App\service\request\handler\HandlerInterface;


abstract class AbstractHandler implements HandlerInterface {
    private AbstractHandler $nextHandler;

    public function setNext(AbstractHandler $handler): void
    {
        $this->nextHandler = $handler;
    }
}