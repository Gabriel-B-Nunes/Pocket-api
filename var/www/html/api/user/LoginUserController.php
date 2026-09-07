<?php

namespace App\api\user;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\api\ControllerInterface;
use App\dao\DAO;
use App\dao\UserDAO;
use App\dto\UserLoginDTO;
use App\exception\BadRequestException;
use App\exception\UnauthorizedException;
use App\exception\ValidationException;
use App\model\Request;
use App\model\User;
use App\service\data\InputHandler;
use App\service\security\HashService;
use App\service\security\JWTService;

class LoginUserController implements ControllerInterface
{
    public function __construct(
        private UserDAO $dao = new UserDAO(),
        private InputHandler $inputHandler = new InputHandler()
    ) {}

    public function handleRequest(Request $request): string
    {
        $data = $request->getData();
        $userLoginDTO = $this->inputHandler->handle(UserLoginDTO::class, $data);

        if ($userLoginDTO) {
            $user = User::loginConstructor($userLoginDTO);
            $userOptional = $this->dao->readByEmail($user)[0] ?? null;

            if ($userOptional) {
                $hashComparation = HashService::verifyPassword($user->getPassword(), $userOptional["password"]);
                $uuid = $userOptional["UUID"];

                if ($hashComparation) {
                    $tokens = JWTService::createToken($uuid);

                    $response = [
                        "Success" => true,
                        "Message" => "Login successful.",
                        "UUID" => $uuid,
                        "Tokens" => $tokens
                    ];

                    http_response_code(200);
                    return json_encode($response);
                }
            }
            throw new UnauthorizedException();
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
