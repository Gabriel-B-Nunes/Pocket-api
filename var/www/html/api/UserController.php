<?php

namespace App\api;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\api\ControllerInterface;
use App\dao\DAO;
use App\dto\UserCreateDTO;
use App\exception\NotFoundException;
use App\exception\ValidationException;
use App\model\Request;
use App\model\User;
use App\service\data\InputHandler;
use Exception;

class UserController implements ControllerInterface
{
    public function __construct (
        private DAO $dao = new DAO(),
        private InputHandler $inputHandler = new InputHandler()
    )
    {}

    public function handleRequest(Request $request): string {

        $method = $request->getMethod();
        $uri = $request->getUri();

        switch (true) {
            case ($method == "POST" && $uri == "/api/user/create"):

                $data = $request->getData();
                $userCreateDTO = $this->inputHandler->handle(UserCreateDTO::class, $data);
                
                if ($userCreateDTO) {
                    try {    
                        $user = User::postConstructor($userCreateDTO);
                        $userOptional = $this->dao->create($user);

                        $response = [
                            "Success" => true,
                            "Message" => "User successfully created",
                            "userId" => $userOptional
                        ];

                        http_response_code(200);
                        return json_encode($response);
                    } catch (Exception $e) {
                        throw new Exception("Internal Server Error", 500);
                    }
                } else {
                    throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
                }

            default:
                throw new NotFoundException();
        }
    }
}
