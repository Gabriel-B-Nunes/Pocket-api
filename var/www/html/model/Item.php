<?php

namespace App\model;

use App\service\security\UUIDService;
use App\dto\item\ItemCreateDTO;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class Item
{
    private function __construct(
        private ?int $id,
        private ?string $uuid,
        private ?string $userUuid,
        private ?string $groupUuid,
        private ?string $name,
        private ?string $status,
    ) {}

    public static function createConstructor(ItemCreateDTO $itemCreateDTO)
    {
        return new self(
            null,
            UUIDService::generate(),
            $itemCreateDTO->userUuid,
            $itemCreateDTO->groupUuid,
            $itemCreateDTO->name,
            $itemCreateDTO->status
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getGroupUuid(): string
    {
        return $this->groupUuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string {
        return $this->status;
    }
}
