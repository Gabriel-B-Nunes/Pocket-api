<?php

namespace App\api\v1\item;

use App\api\ControllerInterface;
use App\dao\ItemDAO;
use App\dto\item\ItemCreateDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Item;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use OpenApi\Attributes as OA;


class CreateItemController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private ItemDAO $dao = new ItemDAO()
    ) {}

#[OA\Post(path: '/api/v1/items', operationId: 'CreateItem', tags: ['Items'], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: \App\dto\item\ItemCreateDTO::class)), security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function handleRequest(Request $request): Response
    {
        $request->setUserUuid();
        $itemCreateDTO = $this->inputHandler->handle(ItemCreateDTO::class, $request->getBody());

        if ($itemCreateDTO) {
            $item = Item::createConstructor($itemCreateDTO);
            $itemOptional = $this->dao->create($item);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                "item" => $itemOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
