<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

enum Unity: string {
    case UN = "UN";
    case KG = "KG";
    case MG = "MG";
    case LT = "LT";
    case ML = "ML";
    case M = "M";
    case CM = "CM";
}