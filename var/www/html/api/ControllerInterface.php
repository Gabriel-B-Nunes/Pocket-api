<?php

namespace App\api;


use App\model\Request;
use App\model\Response;

interface ControllerInterface {
    public function handleRequest(Request $request): Response;
}