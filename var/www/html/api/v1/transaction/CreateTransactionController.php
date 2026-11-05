<?php

declare(strict_types=1);

namespace App\api\v1\transaction;

use App\api\ControllerInterface;
use App\dao\TransactionDAO;
use App\dto\transaction\TransactionCreateDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Transaction;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;
use OpenApi\Attributes as OA;


class CreateTransactionController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private TransactionDAO $dao = new TransactionDAO()
    )
    {}

#[OA\Post(path: '/api/v1/transactions', operationId: 'CreateTransaction', tags: ['Transactions'], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: \App\dto\transaction\TransactionCreateDTO::class)), security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function handleRequest(Request $request): Response
    {
        $request->setUserUuid();

        $transactionCreateDTO = $this->inputHandler->handle(TransactionCreateDTO::class, $request->getBody());

        if ($transactionCreateDTO) {
            $transaction = Transaction::createConstructor($transactionCreateDTO);
            $transactionOptional = $this->dao->create($transaction);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("transaction") => $transactionOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
