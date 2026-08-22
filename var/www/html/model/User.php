<?php

use App\model\AbstractModel;
use App\model\ModelInterface;

class User extends AbstractModel {
    private ?int $userId;
    private ?string $userName;
    private ?string $userEmail;
    private ?string $userCellphoneNumber;
    private ?string $userPassword;
    private ?string $userCreatedAt;

    protected static array $INSERT_VALUES = ["userName", "userEmail", "userCellphoneNumber", "userPassword"];
    protected static string $TABLE_NAME = "user";
    protected static string $PRIMARY_KEY = "userId";

    private function __construct(?int $userId, ?string $userName, ?string $userEmail, ?string $userCellphoneNumber, ?string $userPassword)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userCellphoneNumber = $userCellphoneNumber;
        $this->userPassword = $userPassword;
    }

    public static function getConstructor(array $variables): ModelInterface
    {
        throw new \Exception('Not implemented');
    }

    public static function postConstructor(array $variables): ModelInterface
    {
        throw new \Exception('Not implemented');
    }

    public function getId(): int
    {
        return $this->userId;
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

    public function getCreatedAt(): string
    {
        return htmlspecialchars($this->userCreatedAt);
    }
}