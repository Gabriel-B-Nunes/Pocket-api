<?php

namespace App\dao;

use App\exception\InternalServerErrorException;
use App\exception\UniqueConstraintViolationException;
use App\model\MovementItem;
use App\service\pdo\PDOService;
use PDO;
use PDOException;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class MovementItemDAO {
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PDOService::getInstance();
    }

    public function create(MovementItem $movementItem): mixed {
        $sql = "INSERT INTO movementItem (uuid, userUuid, itemUuid, unitValue, quantity, unity, currency) VALUES (UUID_TO_BIN(:uuid), UUID_TO_BIN(:userUuid), UUID_TO_BIN(:itemUuid), :unitValue, :quantity, :unity, :currency)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $movementItem->getUuid());
        $stmt->bindValue("userUuid", $movementItem->getUserUuid());
        $stmt->bindValue("itemUuid", $movementItem->getItemUuid());
        $stmt->bindValue("unitValue", $movementItem->getUnitValue());
        $stmt->bindValue("quantiy", $movementItem->getQuantity());
        $stmt->bindValue("unity", $movementItem->getCurrency());
        $stmt->bindValue("currency", $movementItem->getCurrency());

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                throw new UniqueConstraintViolationException();
            }

            throw new InternalServerErrorException();
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}