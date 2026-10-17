<?php

namespace App\model;

use App\dto\user\UserCreateDTO;
use App\dto\user\UserLoginDTO;
use App\service\security\HashService;
use App\service\security\UUIDService;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class User {
    private ?int $id;
    private ?string $uuid;
    private ?string $name;
    private ?string $email;
    private ?string $cellphoneNumber;
    private ?string $password;
    private ?string $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    private function __construct(?int $id, ?string $uuid, ?string $name, ?string $email, ?string $cellphoneNumber, ?string $password, ?string $status)
    {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->name = $name;
        $this->email = $email;
        $this->cellphoneNumber = $cellphoneNumber;
        $this->password = $password;
        $this->status = $status;
    }
    
    public static function loginConstructor(UserLoginDTO $UserLoginDTO): self
    {
        return new self (
            null,
            null,
            null,
            $UserLoginDTO->email,
            null,
            $UserLoginDTO->password,
            null
        );
    }

    public static function postConstructor(UserCreateDTO $userCreateDTO): self
    {
        return new self (
            null,
            UUIDService::generate(),
            $userCreateDTO->name,
            $userCreateDTO->email,
            $userCreateDTO->cellphoneNumber,
            HashService::hashPasswordWithPepper($userCreateDTO->password),
            $userCreateDTO->status
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCellphoneNumber(): string
    {
        return $this->cellphoneNumber;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}