<?php

declare(strict_types=1);

namespace App\api\v1\movement;

use App\api\ControllerInterface;
use App\dao\MovementDAO;
use App\dto\movement\MovementReadDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;
use OpenApi\Attributes as OA;


class ReadMovementController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private MovementDAO $dao = new MovementDAO()
    )
    {
    }

#[OA\Get(path: '/api/v1/movements', operationId: 'ReadMovement', tags: ['Movements'], security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function handleRequest(Request $request): Response
    {
        $data = [
            "userUuid" => $request->getUserUuid(),
            "queryString" => $request->getQueryString()
        ];

        $movementReadDTO = $this->inputHandler->handle(MovementReadDTO::class, $data);

        if ($movementReadDTO) {
            $movements = $this->dao->readByQueryString($movementReadDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("movements") => $movements["data"],
                $translator->translate("meta") => $movements["meta"]
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $movements["data"] ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}