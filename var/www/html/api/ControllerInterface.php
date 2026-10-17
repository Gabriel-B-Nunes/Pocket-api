<?php

namespace App\api;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\Request;
use App\model\Response;

interface ControllerInterface {
    public function handleRequest(Request $request): Response;
}