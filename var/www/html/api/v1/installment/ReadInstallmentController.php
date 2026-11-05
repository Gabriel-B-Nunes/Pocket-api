<?php

declare(strict_types=1);

namespace App\api\v1\installment;

use App\api\ControllerInterface;
use App\dao\InstallmentDAO;
use App\dto\installment\InstallmentReadDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;
use OpenApi\Attributes as OA;


class ReadInstallmentController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private InstallmentDAO $dao = new InstallmentDAO()
    )
    {
    }

#[OA\Get(path: '/api/v1/installments', operationId: 'ReadInstallment', tags: ['Installments'], security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'OK')])]
    public function handleRequest(Request $request): Response
    {
        $data = [
            "userUuid" => $request->getUserUuid(),
            "queryString" => $request->getQueryString()
        ];

        $installmentReadDTO = $this->inputHandler->handle(InstallmentReadDTO::class, $data);

        if ($installmentReadDTO) {
            $installments = $this->dao->readByQueryString($installmentReadDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("installments") => $installments["data"],
                $translator->translate("meta") => $installments["meta"]
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $installments["data"] ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}