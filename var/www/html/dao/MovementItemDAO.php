<?php

namespace App\dao;

use App\dto\movementItem\MovementItemReadDTO;
use App\dto\movementItem\MovementItemUpdateDTO;
use App\dto\movementItem\MovementItemUuidDTO;
use App\exception\ForeignKeyConstraintViolationException;
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
        "itemUuid" => ["condition" => " AND itemUuid = UUID_TO_BIN(:itemUuid)", "paramType" => PDO::PARAM_STR],
        "unitValue" => ["condition" => " AND unitValue = :unitValue", "paramType" => PDO::PARAM_INT],
        "quantity" => ["condition" => " AND quantity = :quantity", "paramType" => PDO::PARAM_INT],
        "unity" => ["condition" => " AND unity = :unity", "paramType" => PDO::PARAM_STR],
        "currency" => ["condition" => " AND currency = :currency", "paramType" => PDO::PARAM_STR]
    ];
    const DATE_COLUMNS = [
        "createdAt" => ["condition" => " AND createdAt = :createdAt", "paramType" => PDO::PARAM_STR],
        "createdAt_begin" => ["condition" => " AND createdAt <= :createdAt", "paramType" => PDO::PARAM_STR],
        "createdAt_end" => ["condition" => " AND createdAt >= :createdAt", "paramType" => PDO::PARAM_STR],
        "updatedAt" => ["condition" => " AND updatedAt = :updatedAt", "paramType" => PDO::PARAM_STR],
        "updatedAt_begin" => ["condition" => " AND updatedAt <= :updatedAt", "paramType" => PDO::PARAM_STR],
        "updatedAt_end" => ["condition" => " AND updatedAt >= :updatedAt", "paramType" => PDO::PARAM_STR]
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
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $sql .= $column["condition"];
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
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmt->bindValue(explode("_", $name)[0], $value, $column["paramType"]);
            }
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
        $sql = "SELECT count(*) as pages FROM movementItem WHERE userUuid = UUID_TO_BIN(:userUuid)";

        foreach ($movementItemReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $sql .= $column["condition"];
            }
        }

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("userUuid", $movementItemReadDTO->userUuid);

        foreach ($movementItemReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmt->bindValue(explode("_", $name)[0], $value, $column["paramType"]);
            }
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

    public function update(MovementItemUpdateDTO $movementItemUpdateDTO): mixed {
        $sql = "UPDATE movementItem SET itemUuid = UUID_TO_BIN(:itemUuid), unitValue = :unitValue, quantity = :quantity, unity = :unity, currency = :currency, updatedAt = NOW() WHERE userUuid = UUID_TO_BIN(:userUuid) AND uuid = UUID_TO_BIN(:uuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $movementItemUpdateDTO->uuid);
        $stmt->bindValue("userUuid", $movementItemUpdateDTO->userUuid);
        $stmt->bindValue("itemUuid", $movementItemUpdateDTO->itemUuid);
        $stmt->bindValue("unitValue", $movementItemUpdateDTO->unitValue);
        $stmt->bindValue("quantity", $movementItemUpdateDTO->quantity);
        $stmt->bindValue("unity", $movementItemUpdateDTO->unity);
        $stmt->bindValue("currency", $movementItemUpdateDTO->currency);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new ForeignKeyConstraintViolationException();
        }

        $movementItemUuidDTO = new MovementItemUuidDTO($movementItemUpdateDTO->uuid, $movementItemUpdateDTO->userUuid);

        return self::readByUuid($movementItemUuidDTO);
    }
}