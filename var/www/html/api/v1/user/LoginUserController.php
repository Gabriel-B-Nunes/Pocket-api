<?php

namespace App\api\v1\user;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\api\ControllerInterface;
use App\dao\UserDAO;
use App\dto\user\UserLoginDTO;
use App\exception\UnauthorizedException;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\model\User;
use App\service\cache\Container;
use App\service\data\InputHandler;
use App\service\security\HashService;
use App\service\security\JWTService;
use DateTimeImmutable;

class LoginUserController implements ControllerInterface
{
    public function __construct(
        private UserDAO $dao = new UserDAO(),
        private InputHandler $inputHandler = new InputHandler()
    ) {}

    public function handleRequest(Request $request): Response
    {
        $body = $request->getBody();
        $userLoginDTO = $this->inputHandler->handle(UserLoginDTO::class, $body);

        if ($userLoginDTO) {
            $user = User::loginConstructor($userLoginDTO);
            $userOptional = $this->dao->readByEmail($user);

            if ($userOptional) {
                $hashComparation = HashService::verifyPassword($user->getPassword(), $userOptional["password"]);
                $uuid = $userOptional["uuid"];

                if ($hashComparation) {
                    $tokens = JWTService::createToken($uuid);
                    $translator = Container::get("translator");

                    $response = [
                        $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                        "uuid" => $uuid,
                        "tokens" => $tokens
                    ];

                    return new Response($response, Cache::PRIVATE_NO_CACHE);
                } else {
                    throw new UnauthorizedException();
                }
            } else {
                throw new UnauthorizedException();
            }
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
