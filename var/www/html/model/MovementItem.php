<?php

namespace App\model;

use App\dto\movementItem\MovementItemCreateDTO;
use App\service\security\UUIDService;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

class MovementItem {
    private function __construct(
        private ?int $id,
        private ?string $uuid,
        private ?string $userUuid,
        private ?string $itemUuid,
        private ?int $unitValue,
        private ?int $quantity,
        private ?string $unity,
        private ?string $currency
    )
    {}

    public static function createConstructor(MovementItemCreateDTO $movementItemCreateDTO) {
        return new self (
            null,
            UUIDService::generate(),
            $movementItemCreateDTO->userUuid,
            $movementItemCreateDTO->itemUuid,
            $movementItemCreateDTO->unitValue,
            $movementItemCreateDTO->quantity,
            $movementItemCreateDTO->unity,
            $movementItemCreateDTO->currency
        );
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUuid(): string {
        return $this->uuid;
    }

    public function getUserUuid(): string {
        return $this->userUuid;
    }

    public function getItemUuid(): string {
        return $this->itemUuid;
    }

    public function getUnitValue(): int {
        return $this->unitValue;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function getUnity(): string {
        return $this->unity;
    }

    public function getCurrency(): string {
        return $this->currency;
    }
}