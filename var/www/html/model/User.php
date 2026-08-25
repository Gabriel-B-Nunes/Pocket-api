<?php

namespace App\model;

use App\model\AbstractModel;
use App\service\InputHandler;
use App\dto\UserCreateDTO;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class User extends AbstractModel {
    private ?int $userId;
    private ?string $userName;
    private ?string $userEmail;
    private ?string $userCellphoneNumber;
    private ?string $userPassword;
    private ?int $userStatus;
    private ?string $userCreatedAt;

    protected static array $INSERT_VALUES = ["userName", "userEmail", "userCellphoneNumber", "userPassword"];
    protected static string $TABLE_NAME = "user";
    protected static string $PRIMARY_KEY = "userId";

    private function __construct(?int $userId, ?string $userName, ?string $userEmail, ?string $userCellphoneNumber, ?string $userPassword, ?int $userStatus)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userCellphoneNumber = $userCellphoneNumber;
        $this->userPassword = $userPassword;
        $this->userStatus = $userStatus;
    }

    public static function getConstructor(UserCreateDTO $userCreateDTO): self
    {
        return new self (
        InputHandler::integerValidator(!empty($userCreateDTO->userId) ? $userCreateDTO : null, true),
        InputHandler::alphanumericValidator(!empty($userCreateDTO->userName) ? $userCreateDTO->userName : null, true),
        InputHandler::alphanumericValidator(!empty($userCreateDTO->userEmail) ? $userCreateDTO->userEmail : null, true),
        InputHandler::cellphoneNumberValidator(!empty($userCreateDTO->userCellphoneNumber) ? $userCreateDTO->userCellphoneNumber : null, true),
        InputHandler::stringLengthValidator(!empty($userCreateDTO->userPassword) ? $userCreateDTO : null, ["min" => 12, "max" => 64], true),
        InputHandler::enumValidator(isset($userCreateDTO->userStatus) ? $userCreateDTO->userStatus : null, new StatusEnum, true)
        );
    }

    public static function postConstructor(UserCreateDTO $userCreateDTO): self
    {
        return new self (
        InputHandler::integerValidator(!empty($userCreateDTO->userId) ? $userCreateDTO : null, true),
        InputHandler::alphanumericValidator(!empty($userCreateDTO->userName) ? $userCreateDTO->userName : null),
        InputHandler::alphanumericValidator(!empty($userCreateDTO->userEmail) ? $userCreateDTO->userEmail : null),
        InputHandler::cellphoneNumberValidator(!empty($userCreateDTO->userCellphoneNumber) ? $userCreateDTO->userCellphoneNumber : null),
        InputHandler::stringLengthValidator(!empty($userCreateDTO->userPassword) ? $userCreateDTO->userPassword : null, ["min" => 12, "max" => 64]),
        InputHandler::enumValidator(isset($userCreateDTO->userStatus) ? $userCreateDTO->userStatus : null, new StatusEnum)
        );
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

    public function getStatus(): int
    {
        return $this->userStatus;
    }

    public function getCreatedAt(): string
    {
        return htmlspecialchars($this->userCreatedAt);
    }
}