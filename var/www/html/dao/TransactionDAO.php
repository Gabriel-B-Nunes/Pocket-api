<?php

declare(strict_types=1);

namespace App\dao;

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
}
