<?php

namespace App\api\v1\movementItem;

use App\api\ControllerInterface;
use App\dao\MovementItemDAO;
use App\dto\movementItem\MovementItemReadDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;
use OpenApi\Attributes as OA;


class ReadMovementItemCotroller implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private MovementItemDAO $dao = new MovementItemDAO()
    )
    {}

#[OA\Get(path: '//api/v1/movementItems?|&[wd-]+=[wd-]+*/', operationId: 'ReadMovementItemCotroller', tags: ['MovementItems'], security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function handleRequest(Request $request): Response
    {
        $data = [
            "userUuid" => $request->getUserUuid(),
            "queryString" => $request->getQueryString()
        ];

        $movementItemReadDTO = $this->inputHandler->handle(MovementItemReadDTO::class, $data);

        if ($movementItemReadDTO) {
            $movementItems = $this->dao->readByQueryString($movementItemReadDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("movementItems") => $movementItems["data"],
                $translator->translate("meta") => $movementItems["meta"]
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $movementItems["data"] ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}