<?php

declare(strict_types=1);

namespace App\api\v1\movement;

use App\api\ControllerInterface;
use App\dao\MovementDAO;
use App\dto\movement\MovementCreateDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Movement;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;
use OpenApi\Attributes as OA;


class CreateMovementController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private MovementDAO $dao = new MovementDAO()
    )
    {}

#[OA\Post(path: '/api/v1/movements', operationId: 'CreateMovement', tags: ['Movements'], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: \App\dto\movement\MovementCreateDTO::class)), security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function handleRequest(Request $request): Response
    {
        $request->setUserUuid();

        $movementCreateDTO = $this->inputHandler->handle(MovementCreateDTO::class, $request->getBody());

        if ($movementCreateDTO) {
            $movement = Movement::createConstructor($movementCreateDTO);
            $movementOptional = $this->dao->create($movement);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("movement") => $movementOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
