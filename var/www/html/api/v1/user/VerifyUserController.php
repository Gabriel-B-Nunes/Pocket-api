<?php

namespace App\api\v1\user;

use App\api\ControllerInterface;
use App\dao\UserDAO;
use App\exception\InternalServerErrorException;
use App\exception\NotFoundException;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\model\User;
use App\service\cache\Container;
use App\service\cache\RedisService;
use App\service\data\InputHandler;
use DateTimeImmutable;
use JsonException;

class VerifyUserController implements ControllerInterface
{
    public function __construct(
        private UserDAO $dao = new UserDAO(),
        private InputHandler $inputHandler = new InputHandler()
    ) {}

    public function handleRequest(Request $request): Response
    {
        preg_match("/([\\d\\w]{8}-[\\d\\w]{4}-[\\d\\w]{4}-[\\d\\w]{4}-[\\d\\w]{12})/", $request->getUrl(), $matches);
        $uuid = $matches[1] ?? '';

        if ($uuid === '') {
            throw new ValidationException(message: 'UUID is required.');
        }

        $redis = RedisService::getInstance();
        $cacheKey = 'user:verification:' . $uuid;

        $payload = $redis->get($cacheKey);

        if ($payload === false || $payload === null) {
            throw new NotFoundException(message: 'No pending verification found for the provided uuid.');
        }

        try {
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InternalServerErrorException();
        }

        if (!is_array($data)) {
            throw new InternalServerErrorException();
        }

        $user = User::cacheConstructor($data);

        $userOptional = $this->dao->create($user);

        $redis->del($cacheKey);

        $translator = Container::get("translator");

        $response = [
            $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
            "uuid" => $userOptional
        ];

        return new Response($response, Cache::PRIVATE_NO_CACHE, 200);
    }
}
