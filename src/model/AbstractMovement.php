<?php

namespace App\model;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

abstract class AbstractMovement extends AbstractModel implements ModelInterface
{
    protected ?int $movementId;
    protected ?string $movementDescription;
    protected ?int $movementFinancialType;
    protected ?int $movementRecurring;
    protected ?string $movementIssueDate;
    protected ?string $movementIssueDateEnd;
    protected ?string $movementCreatedAt;
    protected ?string $movementCreatedAtEnd;

    protected static array $validRecurring = [-1, 0, 1];

    protected static array $ILIKE_COLUMNS = ["movementDescription"];
    protected static array $INSERT_VALUES = ["movementDescription", "movementFinancialType", "movementRecurring", "movementIssueDate"];
    protected static array $DATE_COLUMNS = ["movementIssueDate" => "movementIssueDateEnd", "movementCreatedAt" => "movementCreatedAtEnd"];
    protected static array $COLUMNS_WITH_AN_ALL_OPTION = ["movementRecurring"];
    protected static string $PRIMARY_KEY = "movementId";
    protected static string $TABLE_NAME = "movement";

    protected function __construct(?int $movementId, ?string $movementDescription, ?int $movementFinancialType, ?int $movementRecurring, ?string $movementIssueDate, ?string $movementIssueDateEnd, ?string $movementCreatedAt, ?string $movementCreatedAtEnd)
    {
        $this->movementId = $movementId;
        $this->movementDescription = $movementDescription;
        $this->movementFinancialType = $movementFinancialType;
        $this->movementRecurring = $movementRecurring;
        $this->movementIssueDate = $movementIssueDate;
        $this->movementIssueDateEnd = $movementIssueDateEnd;
        $this->movementCreatedAt = $movementCreatedAt;
        $this->movementCreatedAtEnd = $movementCreatedAtEnd;
    }

    public function getId(): ?int
    {
        return $this->movementId;
    }

    public function getDescription(): string
    {
        return htmlspecialchars($this->movementDescription);
    }

    public function getFinancialType(): string
    {
        return htmlspecialchars(FinancialTypeEnum::tryFrom($this->movementFinancialType)->name);
    }

    public function getRecurring(): string
    {
        return htmlspecialchars(RecurringEnum::tryFrom($this->movementRecurring)->name);
    }

    public function getIssueDate(): string
    {
        return htmlspecialchars($this->movementIssueDate);
    }

    public function getCreatedAt(): string
    {
        return htmlspecialchars($this->movementCreatedAt);
    }
}
