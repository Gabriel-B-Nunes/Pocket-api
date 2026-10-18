<?php

namespace App\api\v1\movementItem;

use App\api\ControllerInterface;
use App\dao\MovementItemDAO;
use App\dto\movementItem\MovementItemUpdateDTO;
use App\exception\NotFoundException;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use App\service\security\JWTService;
use DateTimeImmutable;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class UpdateMovementItemController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private MovementItemDAO $dao = new MovementItemDAO()
    ) {}

    public function handleRequest(Request $request): Response
    {
        preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs);

        $data = [
            "uuid" => $matchs[1] ?? "",
            "userUuid" => JWTService::getUserUuid($request->getHeader("Authorization")),
            "itemUuid" => $request->getBody()["itemUuid"] ?? "",
            "unitValue" => $request->getBody()["unitValue"] ?? 0,
            "quantity" => $request->getBody()["quantity"] ?? 0,
            "unity" => $request->getBody()["unity"] ?? "",
            "currency" => $request->getBody()["currency"] ?? ""
        ];

        $movementItemUpdateDTO = $this->inputHandler->handle(MovementItemUpdateDTO::class, $data);

        if ($movementItemUpdateDTO) {
            $movementItemOptional = $this->dao->update($movementItemUpdateDTO);

            if ($movementItemOptional) {
                $translator = Container::get("translator");

                $response = [
                    $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                    $translator->translate("movementItem") => $movementItemOptional
                ];

                return new Response($response, Cache::PRIVATE_NO_CACHE);
            } else {
                throw new NotFoundException();
            }
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
