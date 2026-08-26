<?php

namespace App\api;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\api\ControllerInterface;
use App\dao\DAO;
use App\dto\UserCreateDTO;
use App\model\Request;
use App\model\User;

class UserController implements ControllerInterface
{
    private DAO $dao;

    public function __construct()
    {
        $this->dao = new DAO();
    }

    public function handleRequest(Request $request): string
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        $this->dao = new DAO();

        switch (true) {
            case ($method == "POST" && $uri == "/api/user/create"):
                $data = $request->getData();
                var_dump($data["userName"]);

                $userCreateDTO = new UserCreateDTO(
                    $data["userName"],
                    $data["userEmail"],
                    $data["userCellphoneNumber"],
                    $data["userPassword"],
                    $data["userStatus"]
                );

                $user = User::postConstructor($userCreateDTO);

                $userOptional = $this->dao->create($user);
                return $userOptional;
            
            default:
                return json_encode(["error" => "unknown error"]);
        }
    }
}
