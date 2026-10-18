<?php

namespace App\dao;

use App\dto\item\ItemDeleteDTO;
use App\dto\item\ItemReadDTO;
use App\dto\item\ItemUpdateDTO;
use App\dto\item\ItemUuidDTO;
use App\exception\ForeignKeyConstraintViolationException;
use App\exception\InternalServerErrorException;
use App\exception\UniqueConstraintViolationException;
use App\model\Item;
use App\service\pdo\PDOService;
use PDO;
use PDOException;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class ItemDAO
{
    private PDO $pdo;
    const LIMIT = 50;
    const ILIKE_COLUMNS = [
        "name" => ["condition" => " AND name = :name", "paramType" => PDO::PARAM_STR],
        "status" => ["condition" => " AND status = :status", "paramType" => PDO::PARAM_STR]
    ];
    const DATE_COLUMNS = [
        "createdAt" => ["condition" => " AND createdAt = :createdAt", "paramType" => PDO::PARAM_STR],
        "createdAt_begin" => ["condition" => " AND createdAt >= :createdAt", "paramType" => PDO::PARAM_STR],
        "createdAt_end" => ["condition" => " AND createdAt <= :createdAt", "paramType" => PDO::PARAM_STR],
        "updatedAt" => ["condition" => " AND updatedAt = :updatedAt", "paramType" => PDO::PARAM_STR],
        "updatedAt_begin" => ["condition" => " AND updatedAt >= :updatedAt", "paramType" => PDO::PARAM_STR],
        "updatedAt_end" => ["condition" => " AND updatedAt <= :updatedAt", "paramType" => PDO::PARAM_STR]
    ];

    public function __construct()
    {
        $this->pdo = PDOService::getInstance();
    }

    public function create(Item $item): mixed
    {
        $sql = "INSERT INTO item (uuid, userUuid, groupUuid, name, status) VALUES (UUID_TO_BIN(:uuid), UUID_TO_BIN(:userUuid), UUID_TO_BIN(:groupUuid), :name, :status)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $item->getUuid());
        $stmt->bindValue("userUuid", $item->getUserUuid());
        $stmt->bindValue("groupUuid", $item->getGroupUuid());
        $stmt->bindValue("name", $item->getName());
        $stmt->bindValue("status", $item->getStatus());

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                throw new UniqueConstraintViolationException();
            }

            throw new InternalServerErrorException();
        }

        $itemUuidDTO = new ItemUuidDTO($item->getUuid(), $item->getUserUuid());
        return self::readByUuid($itemUuidDTO);
    }

    public function readByUuid(ItemUuidDTO $itemUuidDTO): mixed
    {
        $sql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(groupUuid) as groupUuid, name, status, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM item WHERE uuid = UUID_TO_BIN(:uuid) AND userUuid = UUID_TO_BIN(:userUuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $itemUuidDTO->uuid);
        $stmt->bindValue("userUuid", $itemUuidDTO->userUuid);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                throw new UniqueConstraintViolationException();
            }

            throw new InternalServerErrorException();
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readByQueryString(ItemReadDTO $itemReadDTO): mixed
    {
        $sql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(groupUuid) as groupUuid, name, status FROM item WHERE userUuid = UUID_TO_BIN(:userUuid)";

        foreach ($itemReadDTO->queryString as $key => $value) {
            $column = self::ILIKE_COLUMNS[$key] ?? self::DATE_COLUMNS[$key] ?? null;
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

        $stmt->bindValue("userUuid", $itemReadDTO->userUuid);
        $stmt->bindValue("limit", self::LIMIT);
        $stmt->bindValue("offset", (($groupReadDTO->queryString["page"] ?? 1) - 1) * self::LIMIT);

        foreach ($itemReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmt->bindValue(explode("_", $name)[0], $value, $column["paramType"]);
            }
        }

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $meta = self::meta($itemReadDTO);

        return ["data" => $data, "meta" => $meta];
    }

    private function meta(ItemReadDTO $itemReadDTO): mixed
    {
        $sql = "SELECT count(*) as pages FROM item WHERE userUuid = UUID_TO_BIN(:userUuid)";

        foreach ($itemReadDTO->queryString as $key => $value) {
            $column = self::ILIKE_COLUMNS[$key] ?? self::DATE_COLUMNS[$key] ?? null;
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

        $stmt->bindValue("userUuid", $itemReadDTO->userUuid);
        $stmt->bindValue("limit", self::LIMIT);
        $stmt->bindValue("offset", (($groupReadDTO->queryString["page"] ?? 1) - 1) * self::LIMIT);

        foreach ($itemReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmt->bindValue(explode("_", $name)[0], $value, $column["paramType"]);
            }
        }

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $pages = $stmt->fetchColumn();

        $meta = [
            "page" => (int) ($itemReadDTO->queryString["page"] ?? 1),
            "limit" => self::LIMIT,
            "totalItems" => $pages ?? 0,
            "totalPages" => $pages ? ceil($pages / self::LIMIT) : 0
        ];

        return $meta;
    }

    public function update(ItemUpdateDTO $itemUpdateDTO): mixed
    {
        $sql = "UPDATE item SET groupUuid = UUID_TO_BIN(:groupUuid), name = :name, status = :status, updatedAt = NOW() WHERE userUuid = UUID_TO_BIN(:userUuid) AND uuid = UUID_TO_BIN(:uuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("groupUuid", $itemUpdateDTO->groupUuid);
        $stmt->bindValue("name", $itemUpdateDTO->name);
        $stmt->bindValue("status", $itemUpdateDTO->status);
        $stmt->bindValue("userUuid", $itemUpdateDTO->userUuid);
        $stmt->bindValue("uuid", $itemUpdateDTO->uuid);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new ForeignKeyConstraintViolationException();
        }
        
        $itemUuidDTO = new ItemUuidDTO($itemUpdateDTO->uuid, $itemUpdateDTO->userUuid);
        $itemOptional = self::readByUuid($itemUuidDTO);

        return $itemOptional;
    }

    public function delete(ItemDeleteDTO $itemDeleteDTO): mixed {
        $sql = "DELETE FROM item WHERE userUuid = UUID_TO_BIN(:userUuid) AND uuid = UUID_TO_BIN(:uuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("userUuid", $itemDeleteDTO->userUuid);
        $stmt->bindValue("uuid", $itemDeleteDTO->uuid);

        $itemUuidDTO = new ItemUuidDTO($itemDeleteDTO->uuid, $itemDeleteDTO->userUuid);
        $itemOptional = self::readByUuid($itemUuidDTO);
        
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new ForeignKeyConstraintViolationException();
        }

        return $itemOptional;
    }
}
