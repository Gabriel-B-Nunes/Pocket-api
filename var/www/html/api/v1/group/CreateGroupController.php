<?php

namespace App\api\v1\group;

use App\api\ControllerInterface;
use App\dao\GroupDAO;
use App\dto\group\GroupCreateDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\Group;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class CreateGroupController implements ControllerInterface
{
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private GroupDAO $dao = new GroupDAO
    ) {}

    public function handleRequest(Request $request): Response
    {
        $request->setUserUuid();
        $groupCreateDTO = $this->inputHandler->handle(GroupCreateDTO::class, $request->getBody());

        if ($groupCreateDTO) {
            $group = Group::createConstructor($groupCreateDTO);
            $groupOptional = $this->dao->create($group);
            
            $translator = Container::get("translator");

            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("group") => $groupOptional
            ];

            return new Response($response, Cache::PRIVATE_NO_CACHE);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}
