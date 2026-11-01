<?php

declare(strict_types=1);

namespace App\dao;

use App\dto\movement\MovementUuidDTO;
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

    public function __construct()
    {
        $this->pdo = PDOService::getInstance();
    }

    public function create(Movement $movement): mixed
    {
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

        return self::readByUuid(new MovementUuidDTO($movement->getUuid(), $movement->getUserUuid()));
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
}
