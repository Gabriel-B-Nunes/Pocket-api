<?php

namespace App\service\handler;

use App\model\Request;
use App\service\Dispatcher;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class DispatchingHandler extends AbstractHandler {

    public function __construct()
    {}

    public function handle(Request $request)
    {
        $dispatcher = new Dispatcher();
        $dispatcher->dispatch($request);
    }
}