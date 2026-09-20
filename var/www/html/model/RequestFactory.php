<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class RequestFactory {
    public static function createGetRequest(string $url, array $queryString): Request {
        return new Request("GET", $url, $queryString);
    }

    public static function createPostRequest(string $url, array $body): Request {
        return new Request("GET", $url, $body);
    }

    public static function createUpdateRequest(string $url, array $body): Request {
        return new Request("GET", $url, $body);
    }

    public static function createDeleteRequest(string $url, array $queryString): Request {
        return new Request("GET", $url, $queryString);
    }
}