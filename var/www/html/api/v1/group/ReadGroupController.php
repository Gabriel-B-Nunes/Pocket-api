<?php

namespace App\api\v1\group;

use App\api\ControllerInterface;
use App\dao\GroupDAO;
use App\dto\group\GroupReadDTO;
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

class ReadGroupController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private GroupDAO $dao = new GroupDAO()
    )
    {}

    public function handleRequest(Request $request): Response
    {
        $data = [
            "userUuid" => JWTService::getUserUuid($request->getHeader("Authorization")),
            "queryString" => $request->getQueryString()
        ];

        $groupReadDTO = $this->inputHandler->handle(GroupReadDTO::class, $data);

        if ($groupReadDTO) {
            $groups = $this->dao->readByQueryString($groupReadDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("groups") => $groups["data"],
                $translator->translate("meta") => $groups["meta"]
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $groups["data"] ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}