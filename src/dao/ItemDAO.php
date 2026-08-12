<?php

namespace App\dao;

use App\model\AbstractModel;
use App\service\PDOService;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class ItemDAO extends DAO
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = PDOService::getInstance();
    }

    public function readByLimitOffset(int $limit, int $offset, AbstractModel $object): ?array
    {
        $sql = "SELECT * FROM `item` i INNER JOIN `group` g ON g.groupId = i.groupId";
        $sql .= parent::prepareCondictions($object->getCondictionPairs(), $object, false, "i");
        $sql .= " ORDER BY i.itemId ASC LIMIT :limit OFFSET :offset";
        error_log($sql);
        $stmt = $this->pdo->prepare($sql);

        $this->bindValues($stmt, $object->getCondictionPairs(), $object, false, "i");

        $stmt->bindValue(":limit", $limit, \PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}