<?php

namespace App\service\request\handler;

use App\model\Request;
use App\service\request\handler\AbstractHandler;
use App\service\security\JWTService;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class AuthenticationHandler extends AbstractHandler
{
    public function __construct(
        private AbstractHandler $nextHandler
    ) {}

    public function handle(Request $request)
    {
        $userRoute = match ($request->getUrl()) {
            "/api/v1/users", "/api/v1/users/login", "/api/v1/users/refresh" => true,
            default => false
        };

        $authorizationHeader = $request->getHeader("Authorization");
        preg_match("/Bearer\s?(.+)/i", $authorizationHeader, $matches);
        $jwtToken = $matches[1] ?? "";

        if (!$userRoute) {
            JWTService::validate($jwtToken);
            $this->nextHandler->handle($request);
            return;
        }

        $this->nextHandler->handle($request);
    }
}
