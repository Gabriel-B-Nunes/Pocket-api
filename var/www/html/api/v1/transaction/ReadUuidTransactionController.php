<?php

declare(strict_types=1);

namespace App\api\v1\transaction;

use App\api\ControllerInterface;
use App\dao\TransactionDAO;
use App\dto\transaction\TransactionUuidDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;
use OpenApi\Attributes as OA;


class ReadUuidTransactionController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private TransactionDAO $dao = new TransactionDAO()
    )
    {}

#[OA\Get(path: '/api/v1/transactions/{uuid}', operationId: 'ReadUuidTransaction', tags: ['Transactions'], parameters: [new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))], security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function handleRequest(Request $request): Response
    {
        preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs);

        $data = [
            "uuid" => $matchs[1] ?? "",
            "userUuid" => $request->getUserUuid()
        ];

        $transactionUuidDTO = $this->inputHandler->handle(TransactionUuidDTO::class, $data);

        if ($transactionUuidDTO) {
            $transactionOptional = $this->dao->readByUuid($transactionUuidDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("transaction") => $transactionOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $transactionOptional ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
