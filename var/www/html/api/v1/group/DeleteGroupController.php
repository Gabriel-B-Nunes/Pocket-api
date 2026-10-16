<?php

namespace App\api\v1\group;

use App\api\ControllerInterface;
use App\dao\GroupDAO;
use App\dto\group\GroupDeleteDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use App\service\security\JWTService;
use DateTimeImmutable;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class DeleteGroupController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private GroupDAO $dao = new GroupDAO()
    )
    {}

    public function handleRequest(Request $request): Response
    {
        preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs);

        $data = [
            "uuid" => $matchs[1] ?? "",
            "userUuid" => JWTService::getUserUuid($request->getHeader("Authorization"))
        ];

        $groupDeleteDTO = $this->inputHandler->handle(GroupDeleteDTO::class, $data);

        if ($groupDeleteDTO) {
            $groupOptional = $this->dao->delete($groupDeleteDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("group") => $groupOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $groupOptional ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}