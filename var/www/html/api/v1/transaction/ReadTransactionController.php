<?php

declare(strict_types=1);

namespace App\api\v1\transaction;

use App\api\ControllerInterface;
use App\dao\TransactionDAO;
use App\dto\transaction\TransactionReadDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;


class ReadTransactionController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private TransactionDAO $dao = new TransactionDAO()
    )
    {
    }

    public function handleRequest(Request $request): Response
    {
        $data = [
            "userUuid" => $request->getUserUuid(),
            "queryString" => $request->getQueryString()
        ];

        $transactionReadDTO = $this->inputHandler->handle(TransactionReadDTO::class, $data);

        if ($transactionReadDTO) {
            $transactions = $this->dao->readByQueryString($transactionReadDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("transactions") => $transactions["data"],
                $translator->translate("meta") => $transactions["meta"]
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $transactions["data"] ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}