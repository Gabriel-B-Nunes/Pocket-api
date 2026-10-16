<?php

namespace App\model;

use App\exception\InternalServerErrorException;
use JsonException;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class Response {
    public function __construct(
        private array $message,
        private Cache $cache,
        private int $code = 200
    )
    {}

    public function getMessage(): array {
        return $this->message;
    }

    public function getCode(): int {
        return $this->code;
    }

    public function getCache(): Cache {
        return $this->cache;
    }

    public function toJson(): string {
        if (empty($this->message)) {
            return "{}";
        }

        try {
            $encode = json_encode($this->message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InternalServerErrorException("The server could not serialize the response.");
        }

        return is_string($encode) ? $encode : "{}";
    }
}