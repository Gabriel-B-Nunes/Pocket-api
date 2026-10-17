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
        "name" => " AND name = :name",
        "status" => " AND status = :status"
    ];
    const DATE_COLUMNS = [
        "createdAt" => " AND createdAt = :createdAt",
        "createdAt_begin" => " AND createdAt >= :createdAt",
        "createdAt_end" => " AND createdAt <= :createdAt",
        "updatedAt" => " AND updatedAt = :updatedAt",
        "updatedAt_begin" => " AND updatedAt >= :updatedAt",
        "updatedAt_end" => " AND updatedAt <= :updatedAt"
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
            if (isset(self::ILIKE_COLUMNS[$key])) {
                $sql .= self::ILIKE_COLUMNS[$key];
            } else if (isset(self::DATE_COLUMNS[$key])) {
                $sql .= self::DATE_COLUMNS[$key];
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
            if (
                isset(self::ILIKE_COLUMNS[$name])
                || isset(self::DATE_COLUMNS[$name])
            )
                $stmt->bindValue(explode("_", $name)[0], $value);
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
            if (isset(self::ILIKE_COLUMNS[$key])) {
                $sql .= self::ILIKE_COLUMNS[$key];
            } else if (isset(self::DATE_COLUMNS[$key])) {
                $sql .= self::DATE_COLUMNS[$key];
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
            if (
                isset(self::ILIKE_COLUMNS[$name])
                || isset(self::DATE_COLUMNS[$name])
            )
                $stmt->bindValue(explode("_", $name)[0], $value);
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
