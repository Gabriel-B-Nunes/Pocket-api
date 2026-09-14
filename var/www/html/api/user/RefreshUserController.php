<?php

namespace App\api\user;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\api\ControllerInterface;
use App\dao\UserDAO;
use App\dto\UserRefreshDTO;
use App\exception\UnauthorizedException;
use App\exception\ValidationException;
use App\model\Request;
use App\service\data\InputHandler;
use App\service\security\JWTService;

class RefreshUserController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler()
    ) {}

    public function handleRequest(Request $request): string
    {
        $data = $request->getData();
        $userRefreshDTO = $this->inputHandler->handle(UserRefreshDTO::class, $data);

        if ($userRefreshDTO) {
            $tokens = JWTService::refresh($userRefreshDTO->refreshToken);

            if ($tokens) {
                $response = [
                    "Success" => true,
                    "Message" => "Refresh successful.",
                    "Tokens" => $tokens
                ];

                http_response_code(200);
                return json_encode($response);
            }

            throw new UnauthorizedException();
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
