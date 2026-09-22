<?php

namespace App\service\handler;

use App\model\Request;
use App\service\handler\AbstractHandler;
use App\service\security\JWTService;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class AuthenticationHandler extends AbstractHandler
{
    public function __construct(
        private AbstractHandler $nextHandler
    ) {}

    public function handle(Request $request)
    {
        $authorizationHeader = $request->getHeader()["Authorization"] ?? "";
        $userRoute = preg_match("/\/api\/user/", $request->getUrl());

        if (!$userRoute) {
            JWTService::validate($authorizationHeader);
            $this->nextHandler->handle($request);
        }

        $this->nextHandler->handle($request);
    }
}
