<?php

namespace App\dao;

use App\dto\movementItem\MovementItemUuidDTO;
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
        $stmt->bindValue("quantity", $movementItem->getQuantity());
        $stmt->bindValue("unity", $movementItem->getUnity());
        $stmt->bindValue("currency", $movementItem->getCurrency());

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                throw new UniqueConstraintViolationException();
            }

            throw new InternalServerErrorException();
        }

        $movementItemUuidDTO = new MovementItemUuidDTO($movementItem->getUuid(), $movementItem->getUserUuid());
        return self::readByUuid($movementItemUuidDTO);
    }

    public function readByUuid(MovementItemUuidDTO $movementItemUuidDTO): mixed {
        $sql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(itemUuid) as itemUuid, unitValue, quantity, unity, currency, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM movementItem WHERE userUuid = UUID_TO_BIN(:userUuid) AND uuid = UUID_TO_BIN(:uuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("userUuid", $movementItemUuidDTO->userUuid);
        $stmt->bindValue("uuid", $movementItemUuidDTO->uuid);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}