<?php

namespace App\api\v1\movementItem;

use App\api\ControllerInterface;
use App\dao\MovementItemDAO;
use App\dto\movementItem\MovementItemCreateDTO;
use App\exception\ValidationException;
use App\model\Cache;
use App\model\MovementItem;
use App\model\Request;
use App\model\Response;
use App\service\cache\Container;
use App\service\data\InputHandler;
use DateTimeImmutable;
use Override;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class CreateMovementItemController implements ControllerInterface {
    public function __construct(
        private InputHandler $inputHandler = new InputHandler(),
        private MovementItemDAO $dao = new MovementItemDAO()
    )
    {}

    public function handleRequest(Request $request): Response
    {
        $request->setUserUuid();
        
        $movementItemCreateDTO = $this->inputHandler->handle(MovementItemCreateDTO::class, $request->getBody());

        if ($movementItemCreateDTO) {
            $movementItem = MovementItem::createConstructor($movementItemCreateDTO);
            $movementItemOptional = $this->dao->create($movementItem);
            $translator = Container::get("translator");
            
            $response = [
                $translator->translate("timestamp") => new DateTimeImmutable()->format("Y-m-d\TH:i:sp"),
                $translator->translate("movementItem") => $movementItemOptional
            ];
            
            return new Response($response, Cache::PRIVATE_NO_CACHE);
        } else {
            throw new ValidationException(errors: $this->inputHandler->getErrorMessages());
        }
    }
}