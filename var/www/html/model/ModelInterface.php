<?php
namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

interface ModelInterface {
    public static function getConstructor(array $variables): self;
    public static function postConstructor(array $variables): self;
}