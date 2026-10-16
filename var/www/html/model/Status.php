<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

enum Status: string {
    case ACTIVE = "ACTIVE";
    case DISABLED = "DISABLED";
}