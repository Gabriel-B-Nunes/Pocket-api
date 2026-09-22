<?php

namespace App\service\handler;

use App\exception\BadRequestException;
use App\exception\InternalServerErrorException;
use App\exception\NotFoundException;
use App\exception\UnauthorizedException;
use App\exception\UniqueConstraintViolationException;
use App\exception\ValidationException;
use App\model\Request;
use App\service\cache\Container;
use App\service\handler\AbstractHandler;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class ExceptionHandler extends AbstractHandler {

    public function __construct(
        private AbstractHandler $nextHandler
    )
    {}

    public function handle(Request $request)
    {
        $translator = Container::get("translator");

        try {
            $this->nextHandler->handle($request);
        } catch (ValidationException $e) {
            $response = [
                $translator->translate("Success") => false,
                $translator->translate("Message") => $translator->translate($e->getMessage()),
                $translator->translate("Errors") => $e->getErrors()
            ];

            http_response_code($e->getCode());
            echo(json_encode($response));
        } catch (UniqueConstraintViolationException
            | UnauthorizedException
            | NotFoundException
            | InternalServerErrorException
            | BadRequestException $e) {
            $response = [
                $translator->translate("Success") => false,
                $translator->translate("Message") => $translator->translate($e->getMessage())
            ];

            http_response_code($e->getCode());
            echo(json_encode($response));
        }
    }
}