<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Request {
    private string $method;
    private string $uri;
    private ?array $data;

    public function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"] ?? 'GET';
        $this->uri = $_SERVER["REQUEST_URI"] ?? '/';
        $this->data = json_decode(file_get_contents("php://input"), true);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPostData(): array
    {
        return $this->data;
    }
}