<?php

namespace App\service\handler;

<<<<<<< HEAD
use App\model\Request;
use App\service\handler\AbstractHandler;
use App\service\handler\AbstractHandler as HandlerAbstractHandler;
use Override;
=======
use App\service\AbstractHandler;
>>>>>>> refactor-request

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class AuthenticationHandler extends AbstractHandler {
<<<<<<< HEAD
    public function __construct(
        private Request $request,
        private HandlerAbstractHandler $nextHandler
    )
    {}

    public function setNext(AbstractHandler $handler): void
    {
        $this->nextHandler = $handler;
    }

    public function handle(Request $request)
    {
        throw new \Exception('Not implemented');
    }
=======

>>>>>>> refactor-request
}