<?php

namespace App\service\request\handler;

use App\model\Request;
use App\service\request\handler\AbstractHandler;
use App\service\security\JWTService;


class AuthenticationHandler extends AbstractHandler
{
    public function __construct(
        private AbstractHandler $nextHandler
    ) {}

    public function handle(Request $request)
    {
        $url = $request->getUrl();

        $userRoute = $url === "/api/v1/users"
            || $url === "/api/v1/users/login"
            || $url === "/api/v1/users/refresh"
            || str_starts_with($url, "/api/v1/users/verify/");

        $authorizationHeader = $request->getHeader("Authorization");
        preg_match("/Bearer\s?(.+)/i", $authorizationHeader, $matches);
        $jwtToken = $matches[1] ?? "";

        if (!$userRoute) {
            JWTService::validate($jwtToken);
            $request->setUserUuid(JWTService::getSubject($jwtToken));
            $this->nextHandler->handle($request);
            return;
        }

        $this->nextHandler->handle($request);
    }
}
