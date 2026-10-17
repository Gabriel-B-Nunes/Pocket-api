<?php

namespace App\service\router;

use App\model\Request;
use App\model\RequestFactory;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class RequestRouter
{
    public function resolveFromGlobals(): Request
    {
        $method = $_SERVER["REQUEST_METHOD"] ?? "GET";
        $url = $_SERVER["REQUEST_URI"] ?? "/";
        $query = $_GET ?? [];
        $rawBody = file_get_contents("php://input") ?? "";

        return match ($method) {
            "GET" => RequestFactory::createGetRequest($url, $query),
            "POST" => RequestFactory::createPostRequest($url, $rawBody),
            "PUT" => RequestFactory::createPutRequest($url, $rawBody),
            "DELETE" => RequestFactory::createDeleteRequest($url, $query),
            default => RequestFactory::createInvalidRequest()
        };
    }
}
