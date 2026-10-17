<?php

namespace App\model;

use App\dto\group\GroupCreateDTO;
use App\service\security\UUIDService;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class Group {
    private function __construct(
        private ?int $id,
        private ?string $uuid,
        private ?string $userUuid,
        private ?string $name,
        private ?string $status
    )
    {}

    public static function createConstructor (GroupCreateDTO $groupCreateDTO): self {
        return new self(
            null,
            UUIDService::generate(),
            $groupCreateDTO->userUuid,
            $groupCreateDTO->name,
            $groupCreateDTO->status
        );
    }

    public function createConstruct() {}

    public function getId(): int {
        return $this->id;
    }

    public function setUuid(string $uuid): void {
        $this->uuid = $uuid;
    }

    public function getUuid(): string {
        return $this->uuid;
    }

    public function getUserUuid(): string {
        return $this->userUuid;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getStatus(): string {
        return $this->status;
    }
}