<?php

namespace App\service;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\control\FrontController;
use App\dao\DAO;
use App\model\AbstractModel;

class ListingManager
{
    private AbstractModel $model;
    private ?int $pages;
    private ?int $currentPage;
    private ?array $queryParams;
    private ?array $registrations;
    private FrontController $controller;
    private DAO $dao;

    private function __construct(AbstractModel $model, ?bool $get = true)
    {
        $this->model = $model;
        $this->controller = new FrontController($model::class);
        $this->dao = new DAO();
        $this->pages = 0;
        $this->currentPage = 1;
        $this->queryParams = [];
        $this->registrations = [];

        if ($get) {
            $_GET = array_merge($_GET, ["action" => "search"]);
            $this->currentPage = max(1, (int)($_GET["page"] ?? 1));
            $this->queryParams = $_GET;
            $this->registrations = $this->controller->handleRequest();
            $this->pages = $this->controller->countPages();
        } else {
            $primaryKey = $model->getPrimaryKey();
            if (isset($_GET[$primaryKey]) && $_GET[$primaryKey] !== "") {
                $this->registrations = $this->controller->getObjectByPrimaryKey() ?? [];
            }
        }
    }

    public static function getConstructor(AbstractModel $model)
    {
        return new self($model);
    }

    public static function postConstructor(AbstractModel $model)
    {
        return new self($model, false);
    }

    public function getClassName(): string
    {
        return $this->model->getClassNameExtended();
    }

    public function getClassViewName(): string
    {
        return $this->model->getClassViewNameExtended();
    }

    public function getDao(): DAO
    {
        return $this->dao;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getRegistrations(): array
    {
        return $this->registrations;
    }

    public function updateQueryParams(array $variables): void
    {
        foreach($variables as $index => $value)
        {
            $this->queryParams[$index] = $value;
        }
    }
}
