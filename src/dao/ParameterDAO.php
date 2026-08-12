<?php

namespace App\dao;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\Parameter;
use App\service\PDOService;

class ParameterDAO extends DAO
{
    private \PDO $pdo;
    private array $ilikeFilters = [];

    public function __construct()
    {
        $this->pdo = PDOService::getInstance();
    }

    public function readAll(): ?array
    {
        $sql = "SELECT name, value FROM parameter";
        $smtp = $this->pdo->prepare($sql);
        $smtp->execute();

        return $smtp->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function updateValue(string $name, string $value): bool
    {
        $sql = "UPDATE parameter SET value = :value WHERE name = :name";
        $smtp = $this->pdo->prepare($sql);
        $smtp->bindValue("value", $value);
        $smtp->bindValue("name", $name);

        return $smtp->execute();
    }
}
