<?php

namespace App\control;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\service\ParameterConfig;
use App\dao\DAO;
use App\dao\ItemDAO;
use App\dao\GroupDAO;

class FrontController
{
    private string $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function handleRequest()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "create") {
            try {
                $object = $this->namespace::postConstructor($_POST);
                $dao = new DAO();

                if (null !== ($object->getId())) {
                    $dao->updateByPrimaryKey($object);
                    header("Location: /{$object->getClassName()}View.php?{$object->getPrimaryKey()}={$object->getId()}");
                    exit();
                }

                $id = $dao->create($object);

                header("Location: /{$object->getClassName()}View.php?{$object->getPrimaryKey()}={$id}");
                exit();
            } catch (\Exception $e) {
                print_r($e->getMessage() . " " . $e->getCode());
                header("Location: /{$object->getClassName()}.php");
                exit();
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && $_GET["action"] == "search") {
            try {
                $object = $this->namespace::getConstructor($_GET);

                switch ($object->getClassName()) {
                    case "group":
                        $dao = new GroupDAO();
                        break;
                    case "item":
                        $dao = new ItemDAO();
                        break;
                    default:
                        $dao = new DAO();
                }

                $currentPage = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
                if ($currentPage < 1) $currentPage = 1;

                $limit = (int)ParameterConfig::getParameterValue("panelMaxRowNumber");

                $offset = ($currentPage - 1) * $limit;

                $result = $dao->readByLimitOffset($limit, $offset, $object);
                return $result;
            } catch (\Exception $e) {
                error_log($e->getMessage() . " " . $e->getCode());
                return [];
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "delete") {
            try {
                $object = $this->namespace::getConstructor($_POST);
                $dao = new DAO();

                $dao->deleteByPrimaryKey($object);
                header("Location: /{$object->getClassName()}.php");
                exit();
            } catch (\Exception $e) {
                error_log($e->getMessage() . " " . $e->getCode());
                header("Location: /{$object->getClassName()}.php");
                exit();
            }
        }

        return [];
    }

    public function countPages(): int
    {
        $object = $this->namespace::getConstructor($_GET);
        $dao = new DAO();

        $result = $dao->count($object);
        return ceil($result / ParameterConfig::getParameterValue("panelMaxRowNumber"));
    }

    public function getObjectByPrimaryKey(): ?array
    {
        $object = $this->namespace::getConstructor($_GET);
        $dao = new DAO();

        $result = $dao->readByPrimaryKey($object);
        return $result;
    }
}
