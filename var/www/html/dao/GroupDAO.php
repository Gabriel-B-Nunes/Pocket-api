<?php

namespace App\dao;

use App\dto\group\GroupDeleteDTO;
use App\dto\group\GroupReadDTO;
use App\dto\group\GroupUpdateDTO;
use App\dto\group\GroupUuidDTO;
use App\exception\ForeignKeyConstraintViolationException;
use App\exception\InternalServerErrorException;
use App\exception\UniqueConstraintViolationException;
use App\model\Group;
use App\service\pdo\PDOService;
use PDO;
use PDOException;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class GroupDAO
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

    public function create(Group $group): mixed
    {
        $sql = "INSERT INTO `group` (uuid, userUuid, name, status) VALUES (UUID_TO_BIN(:uuid), UUID_TO_BIN(:userUuid), :name, :status)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }

        $stmt->bindValue("uuid", $group->getUuid());
        $stmt->bindValue("userUuid", $group->getUserUuid());
        $stmt->bindValue("name", $group->getName());
        $stmt->bindValue("status", $group->getStatus());

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                throw new UniqueConstraintViolationException();
            }

            throw new InternalServerErrorException();
        }

        return self::readByUuid(new GroupUuidDTO($group->getUuid(), $group->getUserUuid()));
    }

    public function readByUuid(GroupUuidDTO $groupUuidDTO): mixed
    {
        $sql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, name, status, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM `group` WHERE uuid = UUID_TO_BIN(:uuid) AND userUuid = UUID_TO_BIN(:userUuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $groupUuidDTO->uuid);
        $stmt->bindValue("userUuid", $groupUuidDTO->userUuid);

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

    public function update(GroupUpdateDTO $groupUpdateDTO): mixed
    {
        $sql = "UPDATE `group` SET name = :name, status = :status, updatedAt = now() WHERE uuid = UUID_TO_BIN(:uuid) AND userUuid = UUID_TO_BIN(:userUuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("name", $groupUpdateDTO->name);
        $stmt->bindValue("status", $groupUpdateDTO->status);
        $stmt->bindValue("uuid", $groupUpdateDTO->uuid);
        $stmt->bindValue("userUuid", $groupUpdateDTO->userUuid);

        try {
            $updateAttempt = $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                throw new UniqueConstraintViolationException();
            }

            throw new InternalServerErrorException();
        }

        $groupUuidDTO = new GroupUuidDTO($groupUpdateDTO->uuid, $groupUpdateDTO->userUuid);

        if ($updateAttempt) {
            return self::readByUuid($groupUuidDTO);
        }

        throw new ForeignKeyConstraintViolationException();
    }

    public function delete(GroupDeleteDTO $groupDeleteDTO): mixed
    {
        $sql = "DELETE FROM `group` WHERE uuid = UUID_TO_BIN(:uuid) AND userUuid = UUID_TO_BIN(:userUuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $groupDeleteDTO->uuid);
        $stmt->bindValue("userUuid", $groupDeleteDTO->userUuid);

        $groupUuidDTO = new GroupUuidDTO($groupDeleteDTO->uuid, $groupDeleteDTO->userUuid);
        $groupOptional = self::readByUuid($groupUuidDTO);

        try {
            $deleteAttempt = $stmt->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException(message: $e->getMessage());
        }

        if ($deleteAttempt) {
            return $groupOptional;
        }

        throw new ForeignKeyConstraintViolationException();
    }

    public function readByQueryString(GroupReadDTO $groupReadDTO): mixed
    {
        $sql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, name, status, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM `group` WHERE userUuid = UUID_TO_BIN(:userUuid)";

        foreach ($groupReadDTO->queryString as $name => $value) {
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

        $stmt->bindValue("userUuid", $groupReadDTO->userUuid);
        $stmt->bindValue("limit", self::LIMIT);
        $stmt->bindValue("offset", (($groupReadDTO->queryString["page"] ?? 1) - 1) * self::LIMIT);

        foreach ($groupReadDTO->queryString as $name => $value) {
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
        $meta = self::meta($groupReadDTO);

        return ["data" => $data, "meta" => $meta];
    }

    private function meta(GroupReadDTO $groupReadDTO): mixed
    {
        $sql = "SELECT count(*) as pages FROM `group` WHERE userUuid = UUID_TO_BIN(:userUuid)";

        foreach ($groupReadDTO->queryString as $name => $value) {
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

        $stmt->bindValue("userUuid", $groupReadDTO->userUuid);

        foreach ($groupReadDTO->queryString as $name => $value) {
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
}
