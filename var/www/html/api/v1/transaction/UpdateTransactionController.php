<?php

declare(strict_types = 1);

namespace App\api\v1\transaction;

use App\api\ControllerInterface;
use App\dao\TransactionDAO;
use App\dto\transaction\TransactionUpdateDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;


class UpdateTransactionController implements ControllerInterface
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

        $transactionUpdateDTO = $this->inputHandler->handle(TransactionUpdateDTO::class, $data);

        if ($transactionUpdateDTO) {
            $transactionOptional = $this->dao->update($transactionUpdateDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("transaction") => $transactionOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, 200);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
