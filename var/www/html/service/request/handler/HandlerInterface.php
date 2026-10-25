<?php

namespace App\service\request\handler;

use App\model\Request;


interface HandlerInterface {
    public function setNext(AbstractHandler $handler): void;
    public function handle(Request $request);
}