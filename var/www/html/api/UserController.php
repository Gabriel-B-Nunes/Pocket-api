<?php

namespace App\api;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\api\ControllerInterface;
use App\dao\DAO;
use App\dto\UserCreateDTO;
use App\dto\UserLoginDTO;
use App\exception\NotFoundException;
use App\exception\UnauthorizedException;
use App\exception\ValidationException;
use App\model\Request;
use App\model\User;
use App\service\data\InputHandler;
use App\service\security\HashService;

class UserController implements ControllerInterface
{
    public function __construct(
        private DAO $dao = new DAO(),
        private InputHandler $inputHandler = new InputHandler()
    ) {}

    public function handleRequest(Request $request): string
    {

        $method = $request->getMethod();
        $uri = $request->getUri();

        switch (true) {
            case ($method == "POST" && $uri == "/api/user/create"):

                $data = $request->getData();
                $userCreateDTO = $this->inputHandler->handle(UserCreateDTO::class, $data);

                if ($userCreateDTO) {
                    $user = User::postConstructor($userCreateDTO);
                    $userOptional = $this->dao->create($user);

                    $response = [
                        "Success" => true,
                        "Message" => "User successfully created.",
                        "userId" => $userOptional
                    ];

                    http_response_code(200);
                    return json_encode($response);
                } else {
                    throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
                }

            case ($method == "POST" && $uri == "/api/user/login"):
                $data = $request->getData();
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
                        } else {
                            throw new UnauthorizedException();
                        }
                    }
                } else {
                    throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
                }

            default:
                throw new NotFoundException();
        }
    }
}
