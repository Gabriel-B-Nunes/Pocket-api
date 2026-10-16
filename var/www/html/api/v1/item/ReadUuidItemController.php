<?php

namespace App\api\v1\item;

use App\api\ControllerInterface;
use App\dao\ItemDAO;
use App\dto\item\ItemResponseDTO;
use App\dto\item\ItemUuidDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use App\service\security\JWTService;
use DateTimeImmutable;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class ReadUuidItemController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private ItemDAO $dao = new ItemDAO()
    )
    {}

    public function handleRequest(Request $request): Response
    {
        preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs);

        $data = [
            "uuid" => $matchs[1] ?? "",
            "userUuid" => JWTService::getUserUuid($request->getHeader("Authorization"))
        ];

        $itemUuidDTO = $this->inputHandler->handle(ItemUuidDTO::class, $data);

        if ($itemUuidDTO) {
            $itemOptional = $this->dao->readByUuid($itemUuidDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("item") => $itemOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $itemOptional ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}