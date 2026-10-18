<?php

namespace App\api\v1\movementItem;

use App\api\ControllerInterface;
use App\dao\MovementItemDAO;
use App\dto\movementItem\MovementItemUuidDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use App\service\security\JWTService;
use DateTimeImmutable;
use Override;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class ReadUuidMovementItemController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private MovementItemDAO $dao = new MovementItemDAO()
    )
    {}

    public function handleRequest(Request $request): Response
    {
        preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs);        
        
        $data = [
            "uuid" => $matchs[1] ?? "",
            "userUuid" => JWTService::getUserUuid($request->getHeader("Authorization"))
        ];

        $movementItemUuidDTO = $this->inputHandler->handle(MovementItemUuidDTO::class, $data);

        if ($movementItemUuidDTO) {
            $movementItemOptional = $this->dao->readByUuid($movementItemUuidDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("movementItem") => $movementItemOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $movementItemOptional ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}