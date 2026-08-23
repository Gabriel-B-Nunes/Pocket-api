<?php

namespace App\api;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\Request;

interface ControllerInterface {
    public function handleRequest(Request $request): string;
}