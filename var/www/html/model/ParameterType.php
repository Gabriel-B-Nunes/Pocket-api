<?php
namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

enum ParameterType: int {
    case INT = 0;
    case FLOAT = 1;
    case BOOL = 2;
    case STR = 3;
    case DATE = 4;
}