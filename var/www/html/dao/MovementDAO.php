<?php

declare(strict_types=1);

namespace App\dao;

use App\dto\movement\MovementReadDTO;
use App\dto\movement\MovementUuidDTO;
use App\exception\ForeignKeyConstraintViolationException;
use App\exception\InternalServerErrorException;
use App\exception\UniqueConstraintViolationException;
use App\model\Movement;
use App\model\MovementItem;
use App\service\pdo\PDOService;
use PDO;
use PDOException;

class MovementDAO
{
    private PDO $pdo;
    const LIMIT = 50;
    const ILIKE_COLUMNS = [
        "description" => ["condition" => " AND description = :description", "paramType" => PDO::PARAM_STR],
        "financialType" => ["condition" => " AND financialType = :financialType", "paramType" => PDO::PARAM_STR],
        "recurrence" => ["condition" => " AND recurrence = :recurrence", "paramType" => PDO::PARAM_STR]
    ];
    const DATE_COLUMNS = [
        "transactionDate" => ["condition" => " AND transactionDate = :transactionDate", "paramType" => PDO::PARAM_STR],
        "transactionDate_begin" => ["condition" => " AND transactionDate <= :transactionDate", "paramType" => PDO::PARAM_STR],
        "transactionDate_end" => ["condition" => " AND transactionDate => :transactionDate", "paramType" => PDO::PARAM_STR],
        "createdAt" => ["condition" => " AND DATE(createdAt) = :createdAt", "paramType" => PDO::PARAM_STR],
        "createdAt_begin" => ["condition" => " AND createdAt <= :createdAt_begin", "paramType" => PDO::PARAM_STR],
        "createdAt_end" => ["condition" => " AND createdAt >= :createdAt_end", "paramType" => PDO::PARAM_STR],
        "updatedAt" => ["condition" => " AND DATE(updatedAt) = :updatedAt", "paramType" => PDO::PARAM_STR],
        "updatedAt_begin" => ["condition" => " AND updatedAt <= :updatedAt_begin", "paramType" => PDO::PARAM_STR],
        "updatedAt_end" => ["condition" => " AND updatedAt >= :updatedAt_end", "paramType" => PDO::PARAM_STR]
    ];

    public function __construct()
    {
        $this->pdo = PDOService::getInstance();
    }

    public function create(Movement $movement): mixed
    {
        $this->pdo->beginTransaction();

        try {
            $sql = "INSERT INTO movement (uuid, userUuid, description, transactionDate, financialType, recurrence) VALUES (UUID_TO_BIN(:uuid), UUID_TO_BIN(:userUuid), :description, :transactionDate, :financialType, :recurrence)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }
        $stmt->bindValue("uuid", $movement->getUuid());
        $stmt->bindValue("userUuid", $movement->getUserUuid());
        $stmt->bindValue("description", $movement->getDescription());
        $stmt->bindValue("transactionDate", $movement->getTransactionDate());
        $stmt->bindValue("financialType", $movement->getFinancialType());
        $stmt->bindValue("recurrence", $movement->getRecurrence());

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1452) {
                    throw new ForeignKeyConstraintViolationException(
                        message: "The referenced resource does not exist.",
                        error: "foreign_key_constraint"
                    );
                }
                throw new UniqueConstraintViolationException();
            }
            throw new InternalServerErrorException();
        }

        $movementItemDAO = new MovementItemDAO();
        foreach ($movement->getMovementItemList() as $item) {
            if ($item instanceof MovementItem) {
                $movementItemDAO->create($item);
            }
        }

        $installmentDAO = new InstallmentDAO();
        foreach ($movement->getInstallmentList() as $installment) {
            if ($installment instanceof \App\model\Installment) {
                $installmentDAO->create($installment);
            }
        }

        $result = self::readByUuid(new MovementUuidDTO($movement->getUuid(), $movement->getUserUuid()));
        $this->pdo->commit();

        return $result;
    } catch (\Throwable $e) {
        $this->pdo->rollBack();
        throw $e;
    }
    }

    public function readByUuid(MovementUuidDTO $movementUuidDTO): mixed
    {
        $sql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, description, transactionDate, financialType, recurrence, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM movement WHERE uuid = UUID_TO_BIN(:uuid) AND userUuid = UUID_TO_BIN(:userUuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $movementUuidDTO->uuid);
        $stmt->bindValue("userUuid", $movementUuidDTO->userUuid);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $movement = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$movement) {
            return null;
        }

        $itemSql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(movementUuid) as movementUuid, BIN_TO_UUID(itemUuid) as itemUuid, unitValue, quantity, unity, currency, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM movementItem WHERE movementUuid = UUID_TO_BIN(:movementUuid) AND userUuid = UUID_TO_BIN(:userUuid)";
        $itemStmt = $this->pdo->prepare($itemSql);
        $itemStmt->bindValue("movementUuid", $movement['uuid']);
        $itemStmt->bindValue("userUuid", $movementUuidDTO->userUuid);
        $itemStmt->execute();
        $movement['movementItems'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        $instSql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(movementUuid) as movementUuid, dueDate, dueValue, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM installment WHERE movementUuid = UUID_TO_BIN(:movementUuid) AND userUuid = UUID_TO_BIN(:userUuid)";
        $instStmt = $this->pdo->prepare($instSql);
        $instStmt->bindValue("movementUuid", $movement['uuid']);
        $instStmt->bindValue("userUuid", $movementUuidDTO->userUuid);
        $instStmt->execute();
        $installments = $instStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($installments as &$inst) {
            $txSql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(installmentUuid) as installmentUuid, paymentDate, paymentValue, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM `transaction` WHERE installmentUuid = UUID_TO_BIN(:installmentUuid) AND userUuid = UUID_TO_BIN(:userUuid)";
            $txStmt = $this->pdo->prepare($txSql);
            $txStmt->bindValue("installmentUuid", $inst['uuid']);
            $txStmt->bindValue("userUuid", $movementUuidDTO->userUuid);
            $txStmt->execute();
            $inst['transactions'] = $txStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $movement['installments'] = $installments;

        return $movement;
    }

    public function readByQueryString(MovementReadDTO $movementReadDTO): mixed
    {
        $select = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, description, transactionDate, financialType, recurrence, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt";
        $from = " FROM movement WHERE userUuid = UUID_TO_BIN(:userUuid)";
        $conditions = "";

        foreach ($movementReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $conditions .= $column["condition"];
            }
        }

        $sql = $select . $from . $conditions;

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("userUuid", $movementReadDTO->userUuid);

        foreach ($movementReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmt->bindValue(explode("_", $name)[0], $value, $column["paramType"]);
            }
        }

        $page = (int) ($movementReadDTO->queryString["page"] ?? 1);
        $offset = ($page - 1) * self::LIMIT;

        $countSql = str_replace($select, "SELECT COUNT(*)", $sql);
        $countSql = rtrim($countSql, ";");

        try {
            $stmtCount = $this->pdo->prepare($countSql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmtCount->bindValue("userUuid", $movementReadDTO->userUuid);

        foreach ($movementReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmtCount->bindValue(explode("_", $name)[0], $value, $column["paramType"]);
            }
        }

        try {
            $stmtCount->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $totalItems = (int) $stmtCount->fetchColumn();

        $sql .= " LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("userUuid", $movementReadDTO->userUuid);

        foreach ($movementReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmt->bindValue(explode("_", $name)[0], $value, $column["paramType"]);
            }
        }

        $stmt->bindValue("limit", self::LIMIT, PDO::PARAM_INT);
        $stmt->bindValue("offset", $offset, PDO::PARAM_INT);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "data" => $data,
            "meta" => self::meta($page, $totalItems)
        ];
    }

    private static function meta(int $page, int $totalItems): array
    {
        return [
            "page" => $page,
            "limit" => self::LIMIT,
            "totalItems" => $totalItems,
            "totalPages" => $totalItems ? ceil($totalItems / self::LIMIT) : 0
        ];
    }
}
