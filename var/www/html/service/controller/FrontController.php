<?php

namespace App\service\controller;

use App\exception\BadRequestException;
use App\exception\ForeignKeyConstraintViolationException;
use App\exception\InternalServerErrorException;
use App\exception\MethodNotAllowedException;
use App\exception\NotFoundException;
use App\exception\UnauthorizedException;
use App\exception\UniqueConstraintViolationException;
use App\exception\ValidationException;
use App\service\cache\Container;
use App\service\request\handler\TranslationHandler;
use App\service\router\RequestRouter;
use App\service\request\handler\AuthenticationHandler;
use App\service\request\handler\ResponseHandler;
use App\service\request\handler\DispatchingHandler;
use App\service\translation\Translator;
use DateTimeImmutable;
use Exception;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class FrontController
{
    public function handle()
    {
        $router = new RequestRouter();

        $chain =
            new TranslationHandler(
                new AuthenticationHandler(
                    new ResponseHandler(
                        new DispatchingHandler()
                    )
                )
            );

        try {
            $request = $router->resolveFromGlobals();
            $chain->handle($request);
        } catch (ValidationException $e) {
            $translator = Container::get("translator");

            if (!$translator) {
                $translator = new Translator();
            }

            $response = [
                $translator->translate("title") => $e->getMessage(),
                $translator->translate("status") => $e->getCode(),
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("errors") => $e->getErrors()
            ];

            http_response_code($e->getCode());
            echo (json_encode($response));
        } catch (
            UniqueConstraintViolationException
            | UnauthorizedException
            | NotFoundException
            | InternalServerErrorException
            | BadRequestException
            | MethodNotAllowedException
            | ForeignKeyConstraintViolationException $e
        ) {
            $translator = Container::get("translator");

            if (!$translator) {
                $translator = new Translator();
            }

            $response = [
                $translator->translate("title") => $e->getMessage(),
                $translator->translate("status") => $e->getCode(),
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp")
            ];

            http_response_code($e->getCode());
            echo (json_encode($response));
        }
    }
}
