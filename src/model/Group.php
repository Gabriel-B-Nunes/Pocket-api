<?php

namespace App\model;

use App\service\UserInputHandler;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Group extends AbstractModel implements ModelInterface
{
    private ?int $groupId;
    private ?string $groupName;
    private ?int $superGroupId;
    private ?int $groupStatus;
    private ?string $groupCreatedAt;
    private ?string $groupCreatedAtEnd;

    private static array $validStatus = [-1, 0, 1];

    protected static array $ILIKE_COLUMNS = ["groupName"];
    protected static array $DATE_COLUMNS = ["groupCreatedAt" => "groupCreatedAtEnd"];
    protected static array $INSERT_VALUES = ["groupName", "superGroupId", "groupStatus"];
    protected static array $COLUMNS_WITH_AN_ALL_OPTION = ["groupStatus", "superGroupId"];
    protected static string $PRIMARY_KEY = "groupId";
    protected static string $TABLE_NAME = "group";

    private function __construct(?int $groupId, ?string $groupName, ?int $superGroupId, ?int $groupStatus, ?string $groupCreatedAt, ?string $groupCreatedAtEnd)
    {
        $this->groupId = $groupId;
        $this->groupName = $groupName;
        $this->superGroupId = $superGroupId;
        $this->groupStatus = $groupStatus;
        $this->groupCreatedAt = $groupCreatedAt;
        $this->groupCreatedAtEnd = $groupCreatedAtEnd;
    }

    public static function getConstructor(array $variables): ModelInterface
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["groupId"]) ? $variables["groupId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["groupName"]) ? $variables["groupName"] : null, true),
            UserInputHandler::integerValidator(!empty($variables["superGroupId"]) ? $variables["superGroupId"] : null, true),
            UserInputHandler::enumValidator(isset($variables["groupStatus"]) ? $variables["groupStatus"] : null, self::$validStatus, new StatusEnum, true),
            UserInputHandler::datetimeValidator(!empty($variables["groupCreatedAt"]) ? $variables["groupCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["groupCreatedAtEnd"]) ? $variables["groupCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
        );
    }

    /**
     * initializes and returns an instance of the Group class.
     * 
     * @return self
     */
    public static function postConstructor(array $variables): ModelInterface
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["groupId"]) ? $variables["groupId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["groupName"]) ? $variables["groupName"] : null),
            UserInputHandler::integerValidator(!empty($variables["superGroupId"]) ? $variables["superGroupId"] : null),
            UserInputHandler::enumValidator(isset($variables["groupStatus"]) ? $variables["groupStatus"] : null, self::$validStatus, new StatusEnum),
            UserInputHandler::datetimeValidator(!empty($variables["groupCreatedAt"]) ? $variables["groupCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["groupCreatedAtEnd"]) ? $variables["groupCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
        );
    }

    public function getId(): ?int
    {
        return $this->groupId;
    }

    public function getName(): string
    {
        return htmlspecialchars($this->groupName);
    }

    public function getSuperGroupId(): ?int
    {
        return $this->superGroupId;
    }

    public function getStatus(): string
    {
        return htmlspecialchars(StatusEnum::tryFrom($this->groupStatus)->name);
    }

    public function getCreatedAt(): string
    {
        return htmlspecialchars($this->groupCreatedAt);
    }
}
