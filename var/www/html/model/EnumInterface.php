<?php
namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

interface EnumInterface
{
    public static function tryFrom(string|int $value);
    public static function cases();
    public static function casesView();
}