<?php

namespace App\model;

use App\model\AbstractModel;
use App\dto\UserCreateDTO;
use App\dto\UserLoginDTO;
use App\service\security\HashService;
use App\service\security\UUIDService;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class User extends AbstractModel {
    private ?int $userId;
    private ?string $userUUID;
    private ?string $userName;
    private ?string $userEmail;
    private ?string $userCellphoneNumber;
    private ?string $userPassword;
    private ?int $userStatus;
    private ?string $userCreatedAt;

    protected static array $INSERT_VALUES = ["userName", "userEmail", "userCellphoneNumber", "userPassword", "userStatus"];
    protected static array $IGNORE_COLUMNS = ["userPassword"];
    protected static string $TABLE_NAME = "user";
    protected static string $PRIMARY_KEY = "userId";

    private function __construct(?int $userId, ?string $userUUID, ?string $userName, ?string $userEmail, ?string $userCellphoneNumber, ?string $userPassword, ?int $userStatus)
    {
        $this->userId = $userId;
        $this->userUUID = $userUUID;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userCellphoneNumber = $userCellphoneNumber;
        $this->userPassword = $userPassword;
        $this->userStatus = $userStatus;
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
        return $this->userId;
    }

    public function getUUID(): string
    {
        return $this->userUUID;
    }

    public function getName(): string
    {
        return htmlspecialchars($this->userName);
    }

    public function getEmail(): string
    {
        return htmlspecialchars($this->userEmail);
    }

    public function getCellphoneNumber(): string
    {
        return htmlspecialchars($this->userCellphoneNumber);
    }

    public function getPassword(): string
    {
        return $this->userPassword;
    }

    public function getStatus(): int
    {
        return $this->userStatus;
    }

    public function getCreatedAt(): string
    {
        return htmlspecialchars($this->userCreatedAt);
    }
}