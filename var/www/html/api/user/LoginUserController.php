<?php

namespace App\api\user;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\api\ControllerInterface;
use App\dao\DAO;
use App\dto\UserLoginDTO;
use App\exception\BadRequestException;
use App\exception\UnauthorizedException;
use App\exception\ValidationException;
use App\model\Request;
use App\model\User;
use App\service\data\InputHandler;
use App\service\security\HashService;

class LoginUserController implements ControllerInterface
{
    public function __construct(
        private DAO $dao = new DAO(),
        private InputHandler $inputHandler = new InputHandler()
    ) {}

    public function handleRequest(Request $request): string
    {
        $data = $request->getData();

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestException(error: json_last_error_msg());
        }

        $userLoginDTO = $this->inputHandler->handle(UserLoginDTO::class, $data);

        if ($userLoginDTO) {
            $user = User::loginConstructor($userLoginDTO);
            $userOptional = $this->dao->readByLimitOffset(1, 0, $user)[0] ?? null;

            if ($userOptional) {
                $hashComparation = HashService::verifyPassword($user->getPassword(), $userOptional["userPassword"]);

                if ($hashComparation) {
                    $response = [
                        "Success" => true,
                        "Message" => "Login successful.",
                        "Token" => "coming soon"
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
