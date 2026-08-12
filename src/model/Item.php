<?php

namespace App\model;

use App\service\UserInputHandler;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Item extends AbstractModel implements ModelInterface
{
    private ?int $itemId;
    private ?string $itemName;
    private ?int $itemMeasurementUnit;
    private ?int $itemStatus;
    private ?int $groupId;
    private ?int $itemFinancialType;
    private ?string $itemCreatedAt;
    private ?string $itemCreatedAtEnd;

    private static array $validMeasurementUnit = [-1, 0, 1, 2];
    private static array $validStatus = [-1, 0, 1];
    private static array $validFinancialType = [-1, 0, 1, 2];

    protected static array $ILIKE_COLUMNS = ["itemName"];
    protected static array $DATE_COLUMNS = ["itemCreatedAt" => "itemCreatedAtEnd"];
    protected static array $INSERT_VALUES = ["itemName", "itemMeasurementUnit", "itemStatus", "groupId", "itemFinancialType"];
    protected static array $COLUMNS_WITH_AN_ALL_OPTION = ["itemMeasurementUnit", "itemStatus", "itemFinancialType", "groupId"];
    protected static string $PRIMARY_KEY = "itemId";
    protected static string $TABLE_NAME = "item";

    private function __construct(?int $itemId, ?string $itemName, ?int $itemMeasurementUnit, ?int $itemStatus, ?int $groupId, ?int $itemFinancialType, ?string $itemCreatedAt, ?string $itemCreatedAtEnd)
    {
        $this->itemId = $itemId;
        $this->itemName = $itemName;
        $this->itemMeasurementUnit = $itemMeasurementUnit;
        $this->itemStatus = $itemStatus;
        $this->groupId = $groupId;
        $this->itemFinancialType = $itemFinancialType;
        $this->itemCreatedAt = $itemCreatedAt;
        $this->itemCreatedAtEnd = $itemCreatedAtEnd;
    }

    public static function getConstructor(array $variables): ModelInterface
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["itemId"]) ? $variables["itemId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["itemName"]) ? $variables["itemName"] : null, true),
            UserInputHandler::enumValidator(isset($variables["itemMeasurementUnit"]) ? $variables["itemMeasurementUnit"] : null, self::$validMeasurementUnit, new MeasurementUnitEnum, true),
            UserInputHandler::enumValidator(isset($variables["itemStatus"]) ? $variables["itemStatus"] : null, self::$validStatus, new StatusEnum, true),
            UserInputHandler::integerValidator(!empty($variables["groupId"]) ? $variables["groupId"] : null, true),
            UserInputHandler::enumValidator(isset($variables["itemFinancialType"]) ? $variables["itemFinancialType"] : null, self::$validFinancialType, new FinancialTypeEnum, true),
            UserInputHandler::datetimeValidator(!empty($variables["itemCreatedAt"]) ? $variables["itemCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["itemCreatedAtEnd"]) ? $variables["itemCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
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
            UserInputHandler::integerValidator(!empty($variables["itemId"]) ? $variables["itemId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["itemName"]) ? $variables["itemName"] : null),
            UserInputHandler::enumValidator(isset($variables["itemMeasurementUnit"]) ? $variables["itemMeasurementUnit"] : null, self::$validMeasurementUnit, new MeasurementUnitEnum),
            UserInputHandler::enumValidator(isset($variables["itemStatus"]) ? $variables["itemStatus"] : null, self::$validStatus, new StatusEnum),
            UserInputHandler::integerValidator(!empty($variables["groupId"]) ? $variables["groupId"] : null),
            UserInputHandler::enumValidator(isset($variables["itemFinancialType"]) ? $variables["itemFinancialType"] : null, self::$validFinancialType, new FinancialTypeEnum),
            UserInputHandler::datetimeValidator(!empty($variables["itemCreatedAt"]) ? $variables["itemCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["itemCreatedAtEnd"]) ? $variables["itemCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
        );
    }

    public function getId(): ?int
    {
        return $this->itemId;
    }

    public function getName(): string
    {
        return htmlspecialchars($this->itemName);
    }

    public function getMeasurementUnit(): string
    {
        return htmlspecialchars(MeasurementUnitEnum::tryFrom($this->itemMeasurementUnit)->name);
    }

    public function getStatus(): string
    {
        return htmlspecialchars(StatusEnum::tryFrom($this->itemStatus)->name);
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getItemFinancialType(): string
    {
        return htmlspecialchars(FinancialTypeEnum::tryFrom($this->itemFinancialType)->name);
    }

    public function getCreatedAt(): string
    {
        return htmlspecialchars($this->itemCreatedAt);
    }
}
