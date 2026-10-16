<?php

namespace App\api\v1\user;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\api\ControllerInterface;
use App\dto\user\UserRefreshDTO;
use App\exception\UnauthorizedException;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use App\service\security\JWTService;
use DateTimeImmutable;

class RefreshUserController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler()
    ) {}

    public function handleRequest(Request $request): Response
    {
        $data = $request->getBody();
        $userRefreshDTO = $this->inputHandler->handle(UserRefreshDTO::class, $data);

        if ($userRefreshDTO) {
            $tokens = JWTService::refresh($userRefreshDTO->refreshToken);

            if ($tokens) {
                $translator = Container::get("translator");

                $response = [
                    $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                    "tokens" => $tokens
                ];

                return new Response($response, Cache::PRIVATE_NO_CACHE);
            } else {
                throw new UnauthorizedException();
            }
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
