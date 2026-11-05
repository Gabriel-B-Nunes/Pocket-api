<?php

namespace App\api\v1\group;

use App\api\ControllerInterface;
use App\dao\GroupDAO;
use App\dto\group\GroupUuidDTO;
use App\exception\NotFoundException;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use OpenApi\Attributes as OA;


class ReadUuidGroupController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private GroupDAO $dao = new GroupDAO()
    ) {}

#[OA\Get(path: '/api/v1/groups/uuid/{uuid}', operationId: 'ReadUuidGroup', tags: ['Groups'], parameters: [new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))], security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function handleRequest(Request $request): Response
    {
        preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs);

        $data = [
            "uuid" => $matchs[1] ?? "",
            "userUuid" => $request->getUserUuid()
        ];
    
        $groupUuidDTO = $this->inputHandler->handle(GroupUuidDTO::class, $data);

        if ($groupUuidDTO) {
            $groupOptional = $this->dao->readByUuid($groupUuidDTO);
            $translator = Container::get("translator");

            if ($groupOptional) {
                $response = [
                    $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                    $translator->translate("group") => $groupOptional
                ];

                return new Response($response, Cache::PRIVATE_NO_CACHE);
            } else {
                throw new NotFoundException(message: "No record found with the provided uuid.");
            }
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
