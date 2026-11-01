<?php

declare(strict_types=1);

namespace App\api\v1\movement;

use App\api\ControllerInterface;
use App\dao\MovementDAO;
use App\dto\movement\MovementUuidDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;


class ReadUuidMovementController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private MovementDAO $dao = new MovementDAO()
    )
    {}

    public function handleRequest(Request $request): Response
    {
        preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs);

        $data = [
            "uuid" => $matchs[1] ?? "",
            "userUuid" => $request->getUserUuid()
        ];

        $movementUuidDTO = $this->inputHandler->handle(MovementUuidDTO::class, $data);

        if ($movementUuidDTO) {
            $movementOptional = $this->dao->readByUuid($movementUuidDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("movement") => $movementOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $movementOptional ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
