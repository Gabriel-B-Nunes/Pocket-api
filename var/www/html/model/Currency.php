<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

enum Currency: string {
    case USD = "USD";
    case BRL = "BRL";
}