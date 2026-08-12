<?php

namespace App\model;

use App\service\UserInputHandler;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class Transaction extends AbstractModel implements ModelInterface
{
    private ?int $transactionId;
    private ?int $transactionValue;

    protected static array $INSERT_VALUES = ["value"];
    protected static string $PRIMARY_KEY = "transactionId";
    protected static string $TABLE_NAME = "transaction";

    private function __construct(?int $transactionId, ?int $value)
    {
        $this->transactionId = $transactionId;
        $this->transactionValue = $value;
    }

    public static function getConstructor(array $variables): self
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["transactionId"]) ? $variables["transactionId"] : null, true),
            UserInputHandler::integerValidator(isset($variables["transactionValue"]) ? $variables["transactionValue"] : null, true),
        );
    }

    public static function postConstructor(array $variables): self
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["transactionId"]) ? $variables["transactionId"] : null, true),
            UserInputHandler::integerValidator(isset($variables["transactionValue"]) ? $variables["transactionValue"] : null),
        );
    }

    public function getId(): ?int
    {
        return $this->transactionId;
    }

    public function getValue(): int
    {
        return $this->transactionValue;
    }
}
