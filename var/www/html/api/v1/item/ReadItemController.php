<?php

namespace App\api\v1\item;

use App\api\ControllerInterface;
use App\dao\ItemDAO;
use App\dto\item\ItemReadDTO;
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

class ReadItemController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private ItemDAO $dao = new ItemDAO()
    )
    {}

    public function handleRequest(Request $request): Response
    {
        $data = [
            "userUuid" => JWTService::getUserUuid($request->getHeader("Authorization")),
            "queryString" => $request->getQueryString()
        ];

        $itemReadDTO = $this->inputHandler->handle(ItemReadDTO::class, $data);

        if ($itemReadDTO) {
            $items = $this->dao->readByQueryString($itemReadDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("items") => $items["data"],
                $translator->translate("meta") => $items["meta"]
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $items["data"] ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}