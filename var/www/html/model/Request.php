<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Request {
    private string $method;
    private string $uri;
    private array $postData;

    public function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"] ?? 'GET';
        $this->uri = $_SERVER["REQUEST_URI"] ?? '/';
        $this->postData = $_POST;
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
        return $this->postData;
    }
}