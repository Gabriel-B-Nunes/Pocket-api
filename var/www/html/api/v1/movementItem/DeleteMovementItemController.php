<?php

namespace App\api\v1\movementItem;

use App\api\ControllerInterface;
use App\dao\MovementItemDAO;
use App\dto\movementItem\MovementItemDeleteDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use OpenApi\Attributes as OA;


class DeleteMovementItemController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private MovementItemDAO $dao = new MovementItemDAO()
    )
    {}

#[OA\Delete(path: '/api/v1/movementItems/uuid/{uuid}', operationId: 'DeleteMovementItem', tags: ['MovementItems'], parameters: [new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))], security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function handleRequest(Request $request): Response
    {
        preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs);

        $data = [
            "uuid" => $matchs[1] ?? "",
            "userUuid" => $request->getUserUuid()
        ];

        $movementItemDeleteDTO = $this->inputHandler->handle(MovementItemDeleteDTO::class, $data);

        if ($movementItemDeleteDTO) {
            $movementItemOptional = $this->dao->delete($movementItemDeleteDTO);
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