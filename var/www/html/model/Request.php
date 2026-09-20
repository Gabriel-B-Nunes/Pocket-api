<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Request {
    
    public function __construct(
        private string $method,
        private string $url,
        private array $headers,
        private ?array $body = [],
        private ?array $queryString = []
    )
    {}

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHeader(): array
    {
        return $this->headers;
    }

    public function getBody(): ?array
    {
        return $this->body;
    }

    public function getQueryString(): ?array
    {
        return $this->queryString;
    }

    public function getModules(): array
    {
        $explodedUri = explode("/", $this->url);
        $modules = array_filter($explodedUri, function($value) {
            return !empty($value);
        });

        return $modules;
    }
}