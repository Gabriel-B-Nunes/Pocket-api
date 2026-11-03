<?php

declare(strict_types=1);

namespace App\model;

use App\dto\movement\MovementCreateDTO;
use App\dto\movementItem\MovementItemCreateDTO;
use App\dto\installment\InstallmentCreateDTO;
use App\exception\ValidationException;
use App\service\data\InputHandler;
use App\service\security\UUIDService;

class Movement implements Payable, Payment
{
    private ?Payment $state = null;

    private array $movementItemList = [];

    private array $installmentList = [];

    public function __construct(
        private ?int $id = null,
        private ?string $uuid = null,
        private ?string $userUuid = null,
        private ?string $description = null,
        private ?string $transactionDate = null,
        ?array $movementItemList = null,
        ?array $installmentList = null,
        ?Payment $state = null,
        private ?string $financialType = null,
        private ?string $recurrence = null
    ) {
        $this->movementItemList = $movementItemList ?? [];
        $this->installmentList = $installmentList ?? [];
        if ($state === null) {
            $paid = $this->getPaymentValue();
            $total = $this->getTotalValue();
            if ($paid === 0) {
                $this->state = new Pending($this);
            } elseif ($paid > 0 && $paid < $total) {
                $this->state = new PartiallyPaid($this);
            } else {
                $this->state = new PaidInFull($this);
            }
        } else {
            $this->state = $state;
            $this->changeState($state);
        }
    }

    public static function createConstructor(MovementCreateDTO $movementCreateDTO): self
    {
        $uuid = UUIDService::generate();

        $items = [];
        foreach ($movementCreateDTO->movementItemList ?? [] as $item) {
            if ($item instanceof MovementItemCreateDTO) {
                $itemCreateDTO = $item;
            } elseif (is_array($item)) {
                $handler = new InputHandler();
                $itemCreateDTO = $handler->handle(MovementItemCreateDTO::class, [
                    'userUuid' => $movementCreateDTO->userUuid,
                    'movementUuid' => $uuid,
                    'itemUuid' => $item['itemUuid'] ?? '',
                    'unitValue' => (int) ($item['unitValue'] ?? 0),
                    'quantity' => (int) ($item['quantity'] ?? 0),
                    'unity' => $item['unity'] ?? '',
                    'currency' => $item['currency'] ?? '',
                ]);
                if ($itemCreateDTO === false) {
                    throw new ValidationException(errors: $handler->getErrorMessages());
                }
            } else {
                continue;
            }
            $items[] = MovementItem::createConstructor($itemCreateDTO);
        }

        $installments = [];
        foreach ($movementCreateDTO->installmentList ?? [] as $inst) {
            if ($inst instanceof InstallmentCreateDTO) {
                $installmentCreateDTO = $inst;
            } elseif (is_array($inst)) {
                $handler = new InputHandler();
                $installmentCreateDTO = $handler->handle(InstallmentCreateDTO::class, [
                    'userUuid' => $movementCreateDTO->userUuid,
                    'movementUuid' => $uuid,
                    'dueDate' => $inst['dueDate'] ?? '',
                    'dueValue' => (int) ($inst['dueValue'] ?? 0),
                    'transactionList' => $inst['transactionList'] ?? null,
                ]);
                if ($installmentCreateDTO === false) {
                    throw new ValidationException(errors: $handler->getErrorMessages());
                }
            } else {
                continue;
            }
            $installments[] = Installment::createConstructor($installmentCreateDTO);
        }

        return new self(
            null,
            $uuid,
            $movementCreateDTO->userUuid,
            $movementCreateDTO->description,
            $movementCreateDTO->transactionDate,
            $items,
            $installments,
            null,
            $movementCreateDTO->financialType,
            $movementCreateDTO->recurrence
        );
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUuid(): ?string { return $this->uuid; }
    public function setUuid(?string $uuid): void { $this->uuid = $uuid; }

    public function getUserUuid(): ?string { return $this->userUuid; }
    public function setUserUuid(?string $userUuid): void { $this->userUuid = $userUuid; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }

    public function getTransactionDate(): ?string { return $this->transactionDate; }
    public function setTransactionDate(?string $transactionDate): void { $this->transactionDate = $transactionDate; }

    public function getMovementItemList(): array { return $this->movementItemList; }
    public function setMovementItemList(?array $movementItemList): void { $this->movementItemList = $movementItemList ?? []; }

    public function getInstallmentList(): array { return $this->installmentList; }
    public function setInstallmentList(?array $installmentList): void { $this->installmentList = $installmentList ?? []; }

    public function getState(): ?Payment { return $this->state; }

    public function getFinancialType(): ?string { return $this->financialType; }
    public function setFinancialType(?string $financialType): void { $this->financialType = $financialType; }

    public function getRecurrence(): ?string { return $this->recurrence; }
    public function setRecurrence(?string $recurrence): void { $this->recurrence = $recurrence; }

    public function getTotalValue(): int
    {
        $sum = 0;
        foreach ($this->movementItemList as $item) {
            if ($item instanceof MovementItem) {
                $sum += ($item->getUnitValue() * $item->getQuantity());
            }
        }
        return $sum;
    }

    public function getPaidValue(): int
    {
        if (!empty($this->installmentList)) {
            $sum = 0;
            foreach ($this->installmentList as $installment) {
                if ($installment instanceof Payable) {
                    $sum += $installment->getPaymentValue();
                }
            }
            return $sum;
        }
        return 0;
    }

    public function getPaymentValue(): int { return $this->getPaidValue(); }

    public function changeState(Payment $state): void
    {
        $paid = $this->getPaidValue();
        $total = $this->getTotalValue();

        if ($state instanceof PaidInFull) {
            if (!($paid > 0 && $paid === $total)) {
                throw new ValidationException('Movement can only be PaidInFull when sum of installments equals total.');
            }
        } elseif ($state instanceof PartiallyPaid) {
            if (!($paid > 0 && $paid < $total)) {
                throw new ValidationException('Movement can only be PartiallyPaid when sum of installments is greater than zero and less than total.');
            }
        } elseif ($state instanceof Pending) {
            if ($paid !== 0) {
                throw new ValidationException('Movement can only be Pending when sum of installments is zero.');
            }
        }

        $this->state = $state;
    }

    public function setState(?Payment $state): void
    {
        if ($state === null) {
            $this->state = null;
            return;
        }
        $this->changeState($state);
    }

    public function payInFull(): void { $this->changeState(new PaidInFull($this)); }
    public function payPartially(): void { $this->changeState(new PartiallyPaid($this)); }
    public function removePayment(): void { $this->changeState(new Pending($this)); }
}
