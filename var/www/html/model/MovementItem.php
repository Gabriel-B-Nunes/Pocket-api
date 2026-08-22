<?php

namespace App\model;

use App\service\UserInputHandler;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

class MovementItem extends AbstractModel implements ModelInterface
{
    private ?int $movementItemId;
    private ?int $movementItemItemId;
    private ?int $movementItemMovementId;
    private ?int $movementItemQuantity;
    private ?int $movementItemItemMeasurementUnity;
    private ?int $movementItemUnityPrice;
    private ?int $movementItemTotalPrice;

    protected static array $INSERT_VALUES = ["movementItemId", "movementItemItemId", "movementItemQuantity", "movementItemUnityPrice", "movementItemTotalPrice"];
    protected static string $PRIMARY_KEY = "movementItemId";
    protected static string $TABLE_NAME = "movementItem";

    private function __construct(?int $movementItemId, ?int $movementItemItemId, ?int $movementItemMovementId, ?int $movementItemQuantity, ?int $movementItemItemMeasurementUnity, ?int $movementItemUnityPrice, ?int $movementItemTotalPrice)
    {
        $this->movementItemId = $movementItemId;
        $this->movementItemItemId = $movementItemItemId;
        $this->movementItemMovementId = $movementItemMovementId;
        $this->movementItemQuantity = $movementItemQuantity;
        $this->movementItemItemMeasurementUnity = $movementItemItemMeasurementUnity;
        $this->movementItemUnityPrice = $movementItemUnityPrice;
        $this->movementItemTotalPrice = $movementItemTotalPrice;
    }

    public static function getConstructor(array $variables): ModelInterface
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["movementItemId"]) ? $variables["movementItemId"] : null, true),
            UserInputHandler::integerValidator(!empty($variables["movementItemItemId"]) ? $variables["movementItemItemId"] : null, true),
            UserInputHandler::integerValidator(!empty($variables["movementItemMovementId"]) ? $variables["movementItemMovementId"] : null, true),
            UserInputHandler::integerValidator(!empty($variables["movementItemQuantity"]) ? $variables["movementItemQuantity"] : null, true),
            UserInputHandler::integerValidator(!empty($variables["movementItemItemMeasurementUnity"]) ? $variables["movementItemItemMeasurementUnity"] : null, true),
            UserInputHandler::integerValidator(!empty($variables["movementItemUnityPrice"]) ? $variables["movementItemUnityPrice"] : null, true),
            UserInputHandler::integerValidator(!empty($variables["movementItemTotalPrice"]) ? $variables["movementItemTotalPrice"] : null, true)
        );
    }

    public static function postConstructor(array $variables): ModelInterface
    {
        return new self(
            UserInputHandler::integerValidator(!empty($variables["movementItemId"]) ? $variables["movementItemId"] : null, true),
            UserInputHandler::integerValidator(!empty($variables["movementItemItemId"]) ? $variables["movementItemItemId"] : null),
            UserInputHandler::integerValidator(!empty($variables["movementItemMovementId"]) ? $variables["movementItemMovementId"] : null),
            UserInputHandler::integerValidator(!empty($variables["movementItemQuantity"]) ? $variables["movementItemQuantity"] : null),
            UserInputHandler::integerValidator(!empty($variables["movementItemItemMeasurementUnity"]) ? $variables["movementItemItemMeasurementUnity"] : null),
            UserInputHandler::integerValidator(!empty($variables["movementItemUnityPrice"]) ? $variables["movementItemUnityPrice"] : null),
            UserInputHandler::integerValidator(!empty($variables["movementItemTotalPrice"]) ? $variables["movementItemTotalPrice"] : null)
        );
    }

    public function getId(): int
    {
        return $this->movementItemId;
    }

    public function getItemId(): int
    {
        return $this->movementItemItemId;
    }

    public function getQuantity(): int
    {
        return $this->movementItemQuantity;
    }

    public function getMeasurementUnity(): string
    {
        return htmlspecialchars(MeasurementUnitEnum::tryFrom($this->movementItemItemMeasurementUnity)->name);
    }

    public function getUnityPrice(): int
    {
        return $this->movementItemUnityPrice;
    }

    public function getTotalPrice(): int
    {
        return $this->movementItemTotalPrice;
    }
}
