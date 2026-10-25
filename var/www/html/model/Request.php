<?php

namespace App\model;


class Request {

    public function __construct(
        private string $method,
        private string $url,
        private array $headers,
        private ?array $body = [],
        private ?array $queryString = [],
        private ?string $userUuid = null
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

    public function getHeaders(): array
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

    public function getHeader(string $header): string {
        return isset($this->headers[$header]) ? $this->headers[$header] : "";
    }

    public function setUserUuid(?string $userUuid = null): void {
        if ($userUuid !== null) {
            $this->userUuid = $userUuid;
        }
        $this->body["userUuid"] = $this->userUuid ?? "";
    }

    public function getUserUuid(): string {
        return $this->userUuid ?? "";
    }
}
