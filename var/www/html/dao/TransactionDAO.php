<?php

declare(strict_types=1);

namespace App\dao;

use App\dto\transaction\TransactionReadDTO;
use App\dto\transaction\TransactionUuidDTO;
use App\exception\InternalServerErrorException;
use App\exception\UniqueConstraintViolationException;
use App\model\Transaction;
use App\service\pdo\PDOService;
use PDO;
use PDOException;

class TransactionDAO
{
    private PDO $pdo;
    const LIMIT = 50;
    const ILIKE_COLUMNS = [
        "paymentDate" => ["condition" => " AND paymentDate = :paymentDate", "paramType" => PDO::PARAM_STR],
        "paymentValue" => ["condition" => " AND paymentValue = :paymentValue", "paramType" => PDO::PARAM_INT]
    ];
    const DATE_COLUMNS = [
        "paymentDate" => ["condition" => " AND paymentDate = :paymentDate", "paramType" => PDO::PARAM_STR],
        "paymentDate_begin" => ["condition" => " AND paymentDate >= :paymentDate_begin", "paramType" => PDO::PARAM_STR],
        "paymentDate_end" => ["condition" => " AND paymentDate <= :paymentDate_end", "paramType" => PDO::PARAM_STR],
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

    public function create(Transaction $transaction): mixed
    {
        $sql = "INSERT INTO `transaction` (uuid, userUuid, installmentUuid, paymentDate, paymentValue) VALUES (UUID_TO_BIN(:uuid), UUID_TO_BIN(:userUuid), UUID_TO_BIN(:installmentUuid), :paymentDate, :paymentValue)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $transaction->getUuid());
        $stmt->bindValue("userUuid", $transaction->getUserUuid());
        $stmt->bindValue("installmentUuid", $transaction->getInstallmentUuid());
        $stmt->bindValue("paymentDate", $transaction->getPaymentDate());
        $stmt->bindValue("paymentValue", $transaction->getPaymentValue());

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                throw new UniqueConstraintViolationException();
            }
            throw new InternalServerErrorException();
        }

        return self::readByUuid(new TransactionUuidDTO($transaction->getUuid(), $transaction->getUserUuid()));
    }

    public function readByUuid(TransactionUuidDTO $transactionUuidDTO): mixed
    {
        $sql = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(installmentUuid) as installmentUuid, paymentDate, paymentValue, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt FROM `transaction` WHERE uuid = UUID_TO_BIN(:uuid) AND userUuid = UUID_TO_BIN(:userUuid)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $transactionUuidDTO->uuid);
        $stmt->bindValue("userUuid", $transactionUuidDTO->userUuid);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readByQueryString(TransactionReadDTO $transactionReadDTO): mixed
    {
        $select = "SELECT id, BIN_TO_UUID(uuid) as uuid, BIN_TO_UUID(userUuid) as userUuid, BIN_TO_UUID(installmentUuid) as installmentUuid, paymentDate, paymentValue, DATE_FORMAT(createdAt, '%Y-%m-%dT%H:%i:%sZ') as createdAt, DATE_FORMAT(updatedAt, '%Y-%m-%dT%H:%i:%sZ') as updatedAt";
        $from = " FROM `transaction` WHERE userUuid = UUID_TO_BIN(:userUuid)";
        $conditions = "";

        foreach ($transactionReadDTO->queryString as $name => $value) {
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

        $stmt->bindValue("userUuid", $transactionReadDTO->userUuid);

        foreach ($transactionReadDTO->queryString as $name => $value) {
            $column = self::ILIKE_COLUMNS[$name] ?? self::DATE_COLUMNS[$name] ?? null;
            if ($column !== null) {
                $stmt->bindValue(":" . $name, $value, $column["paramType"]);
            }
        }

        $page = (int) ($transactionReadDTO->queryString["page"] ?? 1);
        $offset = ($page - 1) * self::LIMIT;

        $countSql = str_replace($select, "SELECT COUNT(*)", $sql);
        $countSql = rtrim($countSql, ";");

        try {
            $stmtCount = $this->pdo->prepare($countSql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmtCount->bindValue("userUuid", $transactionReadDTO->userUuid);

        foreach ($transactionReadDTO->queryString as $name => $value) {
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

        $stmt->bindValue("userUuid", $transactionReadDTO->userUuid);

        foreach ($transactionReadDTO->queryString as $name => $value) {
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
