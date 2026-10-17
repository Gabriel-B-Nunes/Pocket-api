<?php

namespace App\dao;

use App\exception\InternalServerErrorException;
use App\exception\UniqueConstraintViolationException;
use App\model\User;
use App\service\pdo\PDOService;
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

    public function create(User $user): string
    {
        $sql = "INSERT INTO user (uuid, name, email, cellphoneNumber, password, status) VALUES (UUID_TO_BIN(:uuid), :name, :email, :cellphoneNumber, :password, :status)";

        try {
            $stmt = $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        $stmt->bindValue("uuid", $user->getUuid());
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

        return $user->getUuid();
    }

    public function readByEmail(User $user): mixed
    {
        $sql = "SELECT id, BIN_TO_UUID(UUID) as uuid, name, email, cellphoneNumber, password, status FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("email", $user->getEmail());

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new InternalServerErrorException();
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
