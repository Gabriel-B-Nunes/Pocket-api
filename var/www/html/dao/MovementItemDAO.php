<?php

namespace App\dao;

use App\dto\movementItem\MovementItemReadDTO;
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
    const LIMIT = 50;
    const ILIKE_COLUMNS = [
        "unitValue" => " AND unitValue = :unitValue",
        "quantity" => " AND quatity = :quantity",
        "unity" => " AND unity = :unity",
        "currency" => " AND currency = :currency"
    ];
    const DATE_COLUMNS = [
        "createdAt" => " AND createdAt = :createdAt",
        "createdAt_begin" => " AND createdAt <= :createdAt",
        "createdAt_end" => " AND createdAt >= :createdAt",
        "updatedAt" => " AND updatedAt = :updatedAt",
        "updatedAt_begin" => " AND updatedAt <= :updatedAt",
        "updatedAt_end" => " AND updatedAt >= :updatedAt"
    ];

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

    public function readByQueryString(MovementItemReadDTO $movementItemReadDTO): mixed {
        $sql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(itemUuid) as itemUuid, unitValue, quantity, unity, currency, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM movementItem WHERE userUuid = UUID_TO_BIN(:userUuid)";

        foreach ($movementItemReadDTO->queryString as $name => $value) {
            if (isset(self::ILIKE_COLUMNS[$name])) {
                $sql .= self::ILIKE_COLUMNS[$name];
            } else if (isset(self::DATE_COLUMNS[$name])) {
                $sql .= self::DATE_COLUMNS[$name];
            }
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("userUuid", $movementItemReadDTO->userUuid);
        $stmt->bindValue("limit", self::LIMIT);
        $stmt->bindValue("offset", (($movementItemReadDTO->queryString["page"] ?? 1) - 1) * self::LIMIT);

        foreach ($movementItemReadDTO->queryString as $name => $value) {
            if (
                isset(self::ILIKE_COLUMNS[$name])
                || isset(self::DATE_COLUMNS[$name])
            )
                $stmt->bindValue(explode("_", $name)[0], $value);
        }

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                throw new UniqueConstraintViolationException();
            }

            throw new InternalServerErrorException();
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $meta = self::meta($movementItemReadDTO);

        return ["data" => $data, "meta" => $meta];
    }

    private function meta(MovementItemReadDTO $movementItemReadDTO): mixed {
        $sql = "SELECT count(*) as pages FROM `group` WHERE userUuid = UUID_TO_BIN(:userUuid)";

        foreach ($movementItemReadDTO->queryString as $name => $value) {
            if (isset(self::ILIKE_COLUMNS[$name])) {
                $sql .= self::ILIKE_COLUMNS[$name];
            } else if (isset(self::DATE_COLUMNS[$name])) {
                $sql .= self::DATE_COLUMNS[$name];
            }
        }

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("userUuid", $movementItemReadDTO->userUuid);

        foreach ($movementItemReadDTO->queryString as $name => $value) {
            if (
                isset(self::ILIKE_COLUMNS[$name])
                || isset(self::DATE_COLUMNS[$name])
            )
                $stmt->bindValue(explode("_", $name)[0], $value);
        }

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                throw new UniqueConstraintViolationException();
            }

            throw new InternalServerErrorException();
        }

        $pages = $stmt->fetchColumn();

        $meta = [
            "page" => (int) ($groupReadDTO->queryString["page"] ?? 1),
            "limit" => self::LIMIT,
            "totalItems" => $pages ?? 0,
            "totalPages" => $pages ? ceil($pages / self::LIMIT) : 0
        ];

        return $meta;
    }
}