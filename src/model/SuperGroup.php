<?php

namespace App\model;

use App\service\UserInputHandler;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class SuperGroup extends AbstractModel implements ModelInterface
{
    private ?int $superGroupId;
    private ?string $superGroupName;
    private ?int $superGroupStatus;
    private ?string $superGroupCreatedAt;
    private ?string $superGroupCreatedAtEnd;

    private static array $validStatus = [-1, 0, 1];

    protected static array $ILIKE_COLUMNS = ["superGroupName"];
    protected static array $DATE_COLUMNS = ["superGroupCreatedAt" => "superGroupCreatedAtEnd"];
    protected static array $INSERT_VALUES = ["superGroupName", "superGroupStatus"];
    protected static array $COLUMNS_WITH_AN_ALL_OPTION = ["superGroupStatus"];
    protected static string $PRIMARY_KEY = "superGroupId";
    protected static string $TABLE_NAME = "superGroup";

    private function __construct(?int $superGroupId, ?string $superGroupName, ?int $superGroupStatus, ?string $superGroupCreatedAt, ?string  $superGroupCreatedAtEnd)
    {
        $this->superGroupId = $superGroupId;
        $this->superGroupName = $superGroupName;
        $this->superGroupStatus = $superGroupStatus;
        $this->superGroupCreatedAt = $superGroupCreatedAt;
        $this->superGroupCreatedAtEnd = $superGroupCreatedAtEnd;
    }

    public static function getConstructor(array $variables): ModelInterface
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["superGroupId"]) ? $variables["superGroupId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["superGroupName"]) ? $variables["superGroupName"] : null, true),
            UserInputHandler::enumValidator(isset($variables["superGroupStatus"]) ? $variables["superGroupStatus"] : null, self::$validStatus, new StatusEnum, true),
            UserInputHandler::datetimeValidator(!empty($variables["superGroupCreatedAt"]) ? $variables["superGroupCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["superGroupCreatedAtEnd"]) ? $variables["superGroupCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
        );
    }

    /**
     * initializes and returns an instance of the SuperGroup class.
     * 
     * @return self
     */
    public static function postConstructor(array $variables): ModelInterface
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["superGroupId"]) ? $variables["superGroupId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["superGroupName"]) ? $variables["superGroupName"] : null),
            UserInputHandler::enumValidator(isset($variables["superGroupStatus"]) ? $variables["superGroupStatus"] : null, self::$validStatus, new StatusEnum),
            UserInputHandler::datetimeValidator(!empty($variables["superGroupCreatedAt"]) ? $variables["superGroupCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["superGroupCreatedAtEnd"]) ? $variables["superGroupCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
        );
    }

    public function getId(): ?int
    {
        return $this->superGroupId;
    }

    public function getName(): string
    {
        return htmlspecialchars($this->superGroupName);
    }

    public function getStatus(): string
    {
        return htmlspecialchars(StatusEnum::tryFrom($this->superGroupStatus)->name);
    }

    public function getCreatedAt(): string
    {
        return htmlspecialchars($this->superGroupCreatedAt);
    }
}
