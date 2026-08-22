<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\service\UserInputHandler;

class Responsible extends AbstractModel implements ModelInterface
{
    private ?int $responsibleId;
    private ?string $responsibleName;
    private ?string $responsibleEmailAddress;
    private ?string $responsibleCellphoneNumber;
    private ?int $responsibleStatus;
    private ?string $responsibleCreatedAt;
    private ?string $responsibleCreatedAtEnd;

    private static array $validStatus = [-1, 0, 1];

    protected static array $ILIKE_COLUMNS = ["responsibleName", "responsibleEmailAddress"];
    protected static array $DATE_COLUMNS = ["responsibleCreatedAt" => "responsibleCreatedAtEnd"];
    protected static array $INSERT_VALUES = ["responsibleName", "responsibleEmailAddress", "responsibleCellphoneNumber", "responsibleStatus"];
    protected static array $COLUMNS_WITH_AN_ALL_OPTION = ["responsibleStatus"];
    protected static string $PRIMARY_KEY = "responsibleId";
    protected static string $TABLE_NAME = "responsible";

    private function __construct(?int $responsibleId, ?string $responsibleName, ?string $responsibleEmailAddress, ?string $responsibleCellphoneNumber, ?int $responsibleStatus, ?string $responsibleCreatedAt, ?string $responsibleCreatedAtEnd)
    {
        $this->responsibleId = $responsibleId;
        $this->responsibleName = $responsibleName;
        $this->responsibleEmailAddress = $responsibleEmailAddress;
        $this->responsibleCellphoneNumber = $responsibleCellphoneNumber;
        $this->responsibleStatus = $responsibleStatus;
        $this->responsibleCreatedAt = $responsibleCreatedAt;
        $this->responsibleCreatedAtEnd = $responsibleCreatedAtEnd;
    }

    public static function getConstructor(array $variables): self
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["responsibleId"]) ? $variables["responsibleId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["responsibleName"]) ? $variables["responsibleName"] : null, true),
            UserInputHandler::emailValidator(!empty($variables["responsibleEmailAddress"]) ? $variables["responsibleEmailAddress"] : null, true),
            UserInputHandler::cellphoneNumberValidator(!empty($variables["responsibleCellphoneNumber"]) ? $variables["responsibleCellphoneNumber"] : null, true),
            UserInputHandler::enumValidator(isset($variables["responsibleStatus"]) ? $variables["responsibleStatus"] : null, self::$validStatus, new StatusEnum, true),
            UserInputHandler::datetimeValidator(!empty($variables["responsibleCreatedAt"]) ? $variables["responsibleCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["responsibleCreatedAtEnd"]) ? $variables["responsibleCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
        );
    }

    /**
     * initializes and returns an instance of the Responsible class.
     * 
     * @return self
     */
    public static function postConstructor(array $variables): self
    {
        return new self (
            UserInputHandler::integerValidator(!empty($variables["responsibleId"]) ? $variables["responsibleId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["responsibleName"]) ? $variables["responsibleName"] : null),
            UserInputHandler::emailValidator(!empty($variables["responsibleEmailAddress"]) ? $variables["responsibleEmailAddress"] : null, true),
            UserInputHandler::cellphoneNumberValidator(!empty($variables["responsibleCellphoneNumber"]) ? $variables["responsibleCellphoneNumber"] : null, true),
            UserInputHandler::enumValidator(isset($variables["responsibleStatus"]) ? $variables["responsibleStatus"] : null, self::$validStatus, new StatusEnum),
            UserInputHandler::datetimeValidator(!empty($variables["responsibleCreatedAt"]) ? $variables["responsibleCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["responsibleCreatedAtEnd"]) ? $variables["responsibleCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
        );
    }

    public function getId(): ?int
    {
        return $this->responsibleId;
    }

    public function getName(): string
    {
        return htmlspecialchars($this->responsibleName);
    }

    public function getEmailAddress(): ?string
    {
        if (!empty($this->responsibleEmailAddress)) {
            return htmlspecialchars($this->responsibleEmailAddress);
        }
        return $this->responsibleEmailAddress;
    }

    public function getCellphoneNumber(): ?string
    {
        if (!empty($this->responsibleCellphoneNumber)) {
            return htmlspecialchars($this->responsibleCellphoneNumber);
        }
        return $this->responsibleCellphoneNumber;
    }

    public function getStatus(): string
    {
        return htmlspecialchars(StatusEnum::tryFrom($this->responsibleStatus)->name);
    }

    public function getCreatedAt(): string
    {
        return htmlspecialchars($this->responsibleCreatedAt);
    }
}
