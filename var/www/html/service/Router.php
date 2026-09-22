<?php

namespace App\service;

use App\exception\BadRequestException;
use App\model\Request;
use App\model\RequestFactory;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class Router {
    public function resolve(string $method): Request {
        $url = $_SERVER["REQUEST_URI"];

        return match ($method) {
            "GET" => RequestFactory::createGetRequest($url, $_GET),
            "POST" => RequestFactory::createPostRequest($url, json_decode(file_get_contents("php://input"), true)),
            "UPDATE" => RequestFactory::createUpdateRequest($url, json_decode(file_get_contents("php://input"), true)),
            "DELETE" => RequestFactory::createDeleteRequest($url, $_GET),
            default => throw new BadRequestException("The specified method is not supported.")
        };
    }
}