<?php

namespace App\api\v1\user;


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
use OpenApi\Attributes as OA;

class RefreshUserController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler()
    ) {}

#[OA\Post(path: '/api/v1/users/refresh', operationId: 'RefreshUser', tags: ['Users'], security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
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
