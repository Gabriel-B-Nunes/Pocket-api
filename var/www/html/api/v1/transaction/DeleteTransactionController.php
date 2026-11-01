<?php

declare(strict_types = 1);

namespace App\api\v1\transaction;

use App\api\ControllerInterface;
use App\dao\TransactionDAO;
use App\dto\transaction\TransactionDeleteDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;


class DeleteTransactionController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private TransactionDAO $dao = new TransactionDAO()
    )
    {
    }

    public function handleRequest(Request $request): Response
    {
        $uuid = preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs) ? $matchs[1] : null;

        $data = [
            "uuid" => $uuid,
            "userUuid" => $request->getUserUuid()
        ];

        $transactionDeleteDTO = $this->inputHandler->handle(TransactionDeleteDTO::class, $data);

        if ($transactionDeleteDTO) {
            $transactionOptional = $this->dao->readByUuid($transactionDeleteDTO);

            if (!$transactionOptional) {
                throw new ValidationException(errors: ["transaction" => "not_found"]);
            }

            $transactionDeleted = $this->dao->delete($transactionDeleteDTO);

            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
