<?php

declare(strict_types=1);

namespace App\dao;

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
}
