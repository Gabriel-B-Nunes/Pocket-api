<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

interface ModelInterface {
    public function setId(int $id): void;
    public function getId(): int;
}