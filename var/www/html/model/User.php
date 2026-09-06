<?php

namespace App\model;

use App\model\AbstractModel;
use App\dto\UserCreateDTO;
use App\dto\UserLoginDTO;
use App\service\security\HashService;
use App\service\security\UUIDService;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class User extends AbstractModel {
    private ?int $id;
    private ?string $UUID;
    private ?string $name;
    private ?string $email;
    private ?string $cellphoneNumber;
    private ?string $password;
    private ?int $status;
    private ?string $createdAt;

    private function __construct(?int $id, ?string $UUID, ?string $name, ?string $email, ?string $cellphoneNumber, ?string $password, ?int $status)
    {
        $this->id = $id;
        $this->UUID = $UUID;
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
            $UserLoginDTO->userEmail,
            null,
            $UserLoginDTO->userPassword,
            null
        );
    }

    public static function postConstructor(UserCreateDTO $userCreateDTO): self
    {
        return new self (
            null,
            UUIDService::generate(),
            $userCreateDTO->userName,
            $userCreateDTO->userEmail,
            $userCreateDTO->userCellphoneNumber,
            HashService::hashPasswordWithPepper($userCreateDTO->userPassword),
            $userCreateDTO->userStatus
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUUID(): string
    {
        return $this->UUID;
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

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}