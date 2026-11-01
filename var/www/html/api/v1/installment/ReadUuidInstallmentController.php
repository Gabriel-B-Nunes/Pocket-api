<?php

declare(strict_types=1);

namespace App\api\v1\installment;

use App\api\ControllerInterface;
use App\dao\InstallmentDAO;
use App\dto\installment\InstallmentUuidDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;


class ReadUuidInstallmentController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private InstallmentDAO $dao = new InstallmentDAO()
    )
    {}

    public function handleRequest(Request $request): Response
    {
        preg_match("/([\d\w]{8}-[\d\w]{4}-[\d\w]{4}-[\d\w]{4}-[\d\w]{12})/", $request->getUrl(), $matchs);

        $data = [
            "uuid" => $matchs[1] ?? "",
            "userUuid" => $request->getUserUuid()
        ];

        $installmentUuidDTO = $this->inputHandler->handle(InstallmentUuidDTO::class, $data);

        if ($installmentUuidDTO) {
            $installmentOptional = $this->dao->readByUuid($installmentUuidDTO);
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("installment") => $installmentOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE, $installmentOptional ? 200 : 204);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
