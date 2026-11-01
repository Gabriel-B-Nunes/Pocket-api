<?php

declare(strict_types=1);

namespace App\dao;

use App\dto\installment\InstallmentReadDTO;
use App\dto\installment\InstallmentUuidDTO;
use App\exception\InternalServerErrorException;
use App\exception\UniqueConstraintViolationException;
use App\model\Installment;
use App\model\Transaction;
use App\service\pdo\PDOService;
use PDO;
use PDOException;

class InstallmentDAO
{
    private PDO $pdo;
    const LIMIT = 50;
    const ILIKE_COLUMNS = [
        "dueDate" => ["condition" => " AND dueDate = :dueDate", "paramType" => PDO::PARAM_STR],
        "dueValue" => ["condition" => " AND dueValue = :dueValue", "paramType" => PDO::PARAM_INT]
    ];
    const DATE_COLUMNS = [
        "dueDate" => ["condition" => " AND dueDate = :dueDate", "paramType" => PDO::PARAM_STR],
        "dueDate_begin" => ["condition" => " AND dueDate >= :dueDate_begin", "paramType" => PDO::PARAM_STR],
        "dueDate_end" => ["condition" => " AND dueDate <= :dueDate_end", "paramType" => PDO::PARAM_STR],
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

    public function create(Installment $installment): mixed
    {
        $sql = "INSERT INTO installment (uuid, userUuid, movementUuid, dueDate, dueValue) VALUES (UUID_TO_BIN(:uuid), UUID_TO_BIN(:userUuid), UUID_TO_BIN(:movementUuid), :dueDate, :dueValue)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $installment->getUuid());
        $stmt->bindValue("userUuid", $installment->getUserUuid());
        $stmt->bindValue("movementUuid", $installment->getMovementUuid());
        $stmt->bindValue("dueDate", $installment->getDueDate());
        $stmt->bindValue("dueValue", $installment->getDueValue());

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                throw new UniqueConstraintViolationException();
            }
            throw new InternalServerErrorException();
        }

        $transactionDAO = new TransactionDAO();
        foreach ($installment->getTransactionList() as $transaction) {
            if ($transaction instanceof Transaction) {
                $transactionDAO->create($transaction);
            }
        }

        return self::readByUuid(new InstallmentUuidDTO($installment->getUuid(), $installment->getUserUuid()));
    }

    public function readByUuid(InstallmentUuidDTO $installmentUuidDTO): mixed
    {
        $sql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(movementUuid) as movementUuid, dueDate, dueValue, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM installment WHERE uuid = UUID_TO_BIN(:uuid) AND userUuid = UUID_TO_BIN(:userUuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $installmentUuidDTO->uuid);
        $stmt->bindValue("userUuid", $installmentUuidDTO->userUuid);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $installment = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$installment) {
            return null;
        }

        $txSql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(installmentUuid) as installmentUuid, paymentDate, paymentValue, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM `transaction` WHERE installmentUuid = UUID_TO_BIN(:installmentUuid) AND userUuid = UUID_TO_BIN(:userUuid)";
        $txStmt = $this->pdo->prepare($txSql);
        $txStmt->bindValue("installmentUuid", $installment['uuid']);
        $txStmt->bindValue("userUuid", $installmentUuidDTO->userUuid);
        $txStmt->execute();
        $installment['transactions'] = $txStmt->fetchAll(PDO::FETCH_ASSOC);

        return $installment;
    }

    public function readByQueryString(InstallmentReadDTO $installmentReadDTO): mixed
    {
        $select = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(movementUuid) as movementUuid, dueDate, dueValue, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt";
        $from = " FROM installment WHERE userUuid = UUID_TO_BIN(:userUuid)";
        $conditions = "";

        foreach ($installmentReadDTO->queryString as $name => $value) {
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

        $stmt->bindValue("userUuid", $installmentReadDTO->userUuid);

        foreach ($installmentReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmt->bindValue(":" . $name, $value, $column["paramType"]);
            }
        }

        $page = (int) ($installmentReadDTO->queryString["page"] ?? 1);
        $offset = ($page - 1) * self::LIMIT;

        $countSql = str_replace($select, "SELECT COUNT(*)", $sql);
        $countSql = rtrim($countSql, ";");

        try {
            $stmtCount = $this->pdo->prepare($countSql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmtCount->bindValue("userUuid", $installmentReadDTO->userUuid);

        foreach ($installmentReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmtCount->bindValue(":" . $name, $value, $column["paramType"]);
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

        $stmt->bindValue("userUuid", $installmentReadDTO->userUuid);

        foreach ($installmentReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmt->bindValue(":" . $name, $value, $column["paramType"]);
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
