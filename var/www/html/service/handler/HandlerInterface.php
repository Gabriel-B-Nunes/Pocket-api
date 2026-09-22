<?php

namespace App\service\handler;

use App\model\Request;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

interface HandlerInterface {
    public function setNext(AbstractHandler $handler): void;
    public function handle(Request $request);
}