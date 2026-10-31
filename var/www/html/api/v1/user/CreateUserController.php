<?php

namespace App\api\v1\user;


use App\api\ControllerInterface;
use App\dao\UserDAO;
use App\dto\user\UserCreateDTO;
use App\exception\InternalServerErrorException;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\model\User;
use App\service\cache\Container;
use App\service\cache\RedisService;
use App\service\data\InputHandler;
use App\service\PHPMailer\PHPMailerService;
use DateTimeImmutable;
use JsonException;
use PHPMailer\PHPMailer\Exception as MailerException;

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

            $redis = RedisService::getInstance();
            $cacheKey = 'user:verification:' . $user->getUuid();

            try {
                $payload = json_encode([
                    'uuid' => $user->getUuid(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'cellphoneNumber' => $user->getCellphoneNumber(),
                    'password' => $user->getPassword(),
                    'status' => $user->getStatus(),
                ], JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new InternalServerErrorException();
            }

            $redis->setex($cacheKey, 86400, $payload);

            $translator = Container::get("translator");

            if (!$translator) {
                $translator = new \App\service\translation\Translator();
            }

            $appUrl = getenv('APP_URL');
            if ($appUrl === false || $appUrl === '') {
                $appUrl = 'http://localhost:8080';
            }

            $verificationUrl = rtrim($appUrl, '/') . '/api/v1/users/verify/' . $user->getUuid();

            $mailer = PHPMailerService::getInstance();
            $mailer->clearAddresses();
            $mailer->addAddress($user->getEmail());
            $mailer->Subject = $translator->translate('Verify your email');
            $mailer->Body = sprintf($translator->translate('Click the link below to verify your email: %s'), $verificationUrl);
            $mailer->AltBody = sprintf($translator->translate('Verification URL: %s'), $verificationUrl);

            try {
                $mailer->send();
            } catch (MailerException $e) {
                $redis->del($cacheKey);
                throw new InternalServerErrorException();
            }

            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                "uuid" => $user->getUuid()
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, 201);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
