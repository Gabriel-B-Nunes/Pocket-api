<?php

namespace App\model;

use App\exception\BadRequestException;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class RequestFactory {
    public static function createGetRequest(string $url, ?array $queryString): Request {
        return new Request(
            method: "GET", 
            url: $url, 
            headers: getallheaders(),
            queryString: $queryString);
    }

    public static function createPostRequest(string $url, ?string $body): Request {
        return new Request(
            method: "POST", 
            url: $url, 
            headers: getallheaders(),
            body: self::parseJsonBody($body));
    }

    public static function createPutRequest(string $url, ?string $body): Request {
        return new Request(
            method: "PUT", 
            url: $url, 
            headers: getallheaders(),
            body: self::parseJsonBody($body));
    }

    public static function createDeleteRequest(string $url, ?array $queryString): Request {
        return new Request(
            method: "DELETE", 
            url: $url, 
            headers: getallheaders(),
            queryString: $queryString);
    }

    public static function createInvalidRequest(): Request {
        return new Request(
            method: "invalid",
            url: "invalid",
            headers: getallheaders()
        );
    }

    private static function parseJsonBody(?string $body): array {
        if (empty($body)) {
            return [];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestException();
        }

        return is_array($decoded) ? $decoded : [];
    }
}