<?php

namespace App\dao;

use App\model\AbstractModel;
use App\service\PDOService;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class GroupDAO extends DAO
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = PDOService::getInstance();
    }

    public function readByLimitOffset(int $limit, int $offset, AbstractModel $object): ?array
    {
        $sql = "SELECT * FROM `group` g INNER JOIN `superGroup` g2 ON g2.superGroupId = g.superGroupId";
        $sql .= parent::prepareCondictions($object->getCondictionPairs(), $object, false, "g");
        $sql .= " ORDER BY g.groupId ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        $this->bindValues($stmt, $object->getCondictionPairs(), $object, false, "g");

        $stmt->bindValue(":limit", $limit, \PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}