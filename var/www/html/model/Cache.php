<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

enum Cache: string {
    case PRIVATE_NO_CACHE = "no-store, no-cache, must-revalidate, private";
    case PUBLIC_SHORT = "public, max-age=600, must-revalidate";
    case PUBLIC_LONG = "public, max-age=31536000, immutable";
    case CONDITIONAL = "no-cache";
}