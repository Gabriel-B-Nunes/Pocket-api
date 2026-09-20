<?php

namespace App\service\handler;

use App\model\Request;

interface HandlerInterface {
    public function setNext(AbstractHandler $handler): void;
    public function handle(Request $request);
}