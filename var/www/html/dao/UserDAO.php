<?php

namespace App\dao;

use App\exception\InternalServerErrorException;
use App\exception\UniqueConstraintViolationException;
use App\model\User;
use App\service\PDOService;
use PDO;
use PDOException;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class UserDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PDOService::getInstance();
    }

    public function create(User $user): int {
        $sql = "INSERT INTO user (UUID, name, email, cellphoneNumber, password, status) VALUES (UUID_TO_BIN(:UUID), :name, :email, :cellphoneNumber, :password, :status)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("UUID", $user->getUUID());
        $stmt->bindValue("name", $user->getName());
        $stmt->bindValue("email", $user->getEmail());
        $stmt->bindValue("cellphoneNumber", $user->getCellphoneNumber());
        $stmt->bindValue("password", $user->getPassword());
        $stmt->bindValue("status", $user->getStatus());

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                throw new UniqueConstraintViolationException();
            }

            throw new InternalServerErrorException();
        }

        return $this->pdo->lastInsertId();
    }

    public function readByEmail(User $user): array {
        $sql = "SELECT id, BIN_TO_UUID(UUID) as UUID, name, email, cellphoneNumber, password, status FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("email", $user->getEmail());

        try {
            $stmt->execute();
        } catch(PDOException $e) {
            throw new InternalServerErrorException();
        }

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

        return $row;
    }
}