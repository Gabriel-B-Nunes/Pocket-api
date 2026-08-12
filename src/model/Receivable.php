<?php

namespace App\model;

use App\service\UserInputHandler;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Receivable extends AbstractMovement
{
    public static function getConstructor(array $variables): ModelInterface
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["movementId"]) ? $variables["movementId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["movementDescription"]) ? $variables["movementDescription"] : null, true),
            FinancialTypeEnum::tryFrom(0)->value,
            UserInputHandler::enumValidator(isset($variables["movementRecurring"]) ? $variables["movementRecurring"] : null, parent::$validRecurring, new RecurringEnum, true),
            UserInputHandler::datetimeValidator(!empty($variables["movementIssueDate"]) ? $variables["movementIssueDate"] : null, "Y-m-d", true),
            UserInputHandler::datetimeValidator(!empty($variables["movementIssueDateEnd"]) ? $variables["movementIssueDateEnd"] : null, "Y-m-d", true),
            UserInputHandler::datetimeValidator(!empty($variables["movementCreatedAt"]) ? $variables["movementCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["movementCreatedAtEnd"]) ? $variables["movementCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
        );
    }

    /**
     * initializes and returns an instance of the Movement class.
     * 
     * @return self
     */
    public static function postConstructor(array $variables): ModelInterface
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["movementId"]) ? $variables["movementId"] : null, true),
            UserInputHandler::alphanumericValidator(!empty($variables["movementDescription"]) ? $variables["movementDescription"] : null),
            FinancialTypeEnum::tryFrom(0)->value,
            UserInputHandler::enumValidator(isset($variables["movementRecurring"]) ? $variables["movementRecurring"] : null, parent::$validRecurring, new RecurringEnum),
            UserInputHandler::datetimeValidator(!empty($variables["movementIssueDate"]) ? $variables["movementIssueDate"] : null, "Y-m-d"),
            UserInputHandler::datetimeValidator(!empty($variables["movementIssueDateEnd"]) ? $variables["movementIssueDateEnd"] : null, "Y-m-d", true),
            UserInputHandler::datetimeValidator(!empty($variables["movementCreatedAt"]) ? $variables["movementCreatedAt"] : null, "Y-m-d H:i:s", true),
            UserInputHandler::datetimeValidator(!empty($variables["movementCreatedAtEnd"]) ? $variables["movementCreatedAtEnd"] : null, "Y-m-d H:i:s", true)
        );
    }
}
