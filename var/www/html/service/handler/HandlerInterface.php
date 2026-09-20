<?php

namespace App\service\handler;

use App\model\Request;

interface HandlerInterface {
    public function setNext(): void;
    public function handle(Request $request);
}