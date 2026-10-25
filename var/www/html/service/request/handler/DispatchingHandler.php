<?php

namespace App\service\request\handler;

use App\model\Request;
use App\model\Response;
use App\service\Dispatcher;


class DispatchingHandler extends AbstractHandler {

    public function __construct()
    {}

    public function handle(Request $request): Response
    {
        $dispatcher = new Dispatcher();
        return $dispatcher->dispatch($request);
    }
}