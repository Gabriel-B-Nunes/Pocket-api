<?php

namespace App\dao;

use App\model\AbstractModel;
use App\service\PDOService;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class DAO
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = PDOService::getInstance();
    }

    public function readByLimitOffset(int $limit, int $offset, AbstractModel $object): ?array
    {
        $sql = "SELECT *";
        $sql .= " FROM `" . $object->getTableName() . "`";
        $sql .= self::prepareCondictions($object->getCondictionPairs(), $object);
        $sql .= " ORDER BY `" . implode(", ", array_keys($object->getPrimaryKeyPairs())) . "`";
        $sql .= " ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        $this->bindValues($stmt, $object->getCondictionPairs(), $object);

        $stmt->bindValue(":limit", $limit, \PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function count(AbstractModel $object): int
    {
        $sql = "SELECT count(*)";
        $sql .= " FROM `" . $object->getTableName() . "`";
        $sql .= self::prepareCondictions($object->getCondictionPairs(), $object);

        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt, $object->getCondictionPairs(), $object);

        $stmt->execute();
        $result = $stmt->fetchColumn();

        return $result;
    }

    public function create(AbstractModel $object): ?int
    {
        $sql = "INSERT INTO `" . $object->getTableName() . "`";
        $sql .= " (" . implode(", ", array_keys($object->getInsertValuesPairs()));
        $sql .= ") VALUES (" . self::prepareInsertValues(array_keys($object->getInsertValuesPairs())) . ")";

        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt, $object->getInsertValuesPairs(), $object, true);
        
        $insertBool = $stmt->execute();

        if ($insertBool) {
            $id = $this->pdo->lastInsertId();
            return $id;
        }
        
        return null;
    }

    public function readByPrimaryKey(AbstractModel $object): ?array
    {
        $sql = "SELECT *";
        $sql .= " FROM `" . $object->getTableName() . "`";
        $sql .= self::prepareCondictions($object->getPrimaryKeyPairs(), $object, true);

        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt, $object->getPrimaryKeyPairs(), $object, true);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateByPrimaryKey(AbstractModel $object): bool
    {
        $sql = "UPDATE `" . $object->getTableName() . "`";
        $sql .= self::prepareUpdateValues(array_keys($object->getInsertValuesPairs()));
        $sql .= self::prepareCondictions($object->getPrimaryKeyPairs(), $object, true);

        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt, $object->getCondictionPairs(), $object, true);

        return $stmt->execute();
    }

    public function deleteByPrimaryKey(AbstractModel $object): bool
    {
        $sql = "DELETE FROM `" . $object->getTableName() . "`";
        $sql .= self::prepareCondictions($object->getPrimaryKeyPairs(), $object, true);

        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt, $object->getPrimaryKeyPairs(), $object, true);

        return $stmt->execute();
    }

    public function readMaxPrimaryKey(AbstractModel $object): int
    {
        $sql = "SELECT max(`" . implode(", ", array_keys($object->getPrimaryKeyPairs())) . "`)";
        $sql .= " FROM `" . $object->getTableName() . "`";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function readAllStatusActive(AbstractModel $object): ?array
    {
        $sql = "SELECT *";
        $sql .= " FROM `" . $object->getTableName() . "`";
        $sql .= " WHERE " . $object->getTableName() . "Status = 0";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function prepareCondictions(array $condictionsArray, ?AbstractModel $object, bool $exactMatch = false, ?string $tableAlias = null): ?string
    {
        if (!empty($condictionsArray)) {
            $condictions = [];
            $prefix = $tableAlias ? "`{$tableAlias}`." : "";

            foreach ($condictionsArray as $filter => $value) {
                if ($exactMatch === false && in_array($filter, $object->getIlikeColumns())) {
                    $condictions[] = "{$prefix}`{$filter}` LIKE :{$filter}";
                } else if (in_array($filter, $object->getColumnsWithAnAllOption()) && $value === -1 || in_array($filter, $object->getDateColumns())) {
                    continue;
                } else if (array_key_exists($filter, $object->getDateColumns())) {
                    $condictions[] = "{$prefix}`{$filter}` BETWEEN :{$filter} AND :{$object->getDateColumns()[$filter]}";
                } else {
                    $condictions[] = "{$prefix}`{$filter}` = :{$filter}";
                }
            }

            if (!empty($condictions)) {
                return " WHERE " . implode(" AND ", $condictions);
            }
        }

        return null;
    }

    protected function prepareInsertValues(array $insertValues): ?string
    {
        if (!empty($insertValues)) {
            $values = [];

            foreach ($insertValues as $value) {
                $values[] = ":{$value}";
            }

            if (!empty($values)) {
                return implode(", ", $values);
            }
        }

        return null;
    }

    protected function prepareUpdateValues(array $updateValues): ?string
    {
        if (!empty($updateValues)) {
            $values = [];

            foreach ($updateValues as $value) {
                $values[] = "`{$value}` = :{$value}";
            }

            if (!empty($values)) {
                return " SET " . implode(", ", $values);
            }
        }

        return null;
    }

    protected function bindValues(\PDOStatement $stmt, array $bindValues, ?AbstractModel $object, bool $exactMatch = false, ?string $tableAlias = null)
    {
        if (!empty($bindValues)) {
            foreach ($bindValues as $filter => $value) {
                if ($exactMatch === false && in_array($filter, $object->getIlikeColumns())) {
                    $stmt->bindValue(":{$filter}", "%{$value}%");
                } else if (in_array($filter, $object->getColumnsWithAnAllOption()) && $value === -1) {
                    continue;
                } else {
                    $stmt->bindValue(":{$filter}", $value);
                }
            }
        }
    }
}
