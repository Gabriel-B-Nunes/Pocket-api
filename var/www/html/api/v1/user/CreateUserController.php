<?php

namespace App\api\v1\user;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\api\ControllerInterface;
use App\dao\UserDAO;
use App\dto\user\UserCreateDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\model\User;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;

class CreateUserController implements ControllerInterface
{
    public function __construct(
        private UserDAO $dao = new UserDAO(),
        private InputHandler $inputHandler = new InputHandler()
    ) {}

    public function handleRequest(Request $request): Response
    {
        $data = $request->getBody();
        $userCreateDTO = $this->inputHandler->handle(UserCreateDTO::class, $data);

        if ($userCreateDTO) {
            $user = User::postConstructor($userCreateDTO);
            $userOptional = $this->dao->create($user);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                "uuid" => $userOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, 201);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
