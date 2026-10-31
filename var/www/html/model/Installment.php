<?php

declare(strict_types=1);

namespace App\model;

use App\exception\ValidationException;

class Installment implements Payable, Payment
{
    private ?Payment $state = null;

    private array $transactionList = [];

    public function __construct(
        private ?int $id = null,
        private ?string $uuid = null,
        private ?string $userUuid = null,
        private ?string $dueDate = null,
        private ?int $dueValue = null,
        ?array $transactionList = null,
        ?Payment $state = null
    ) {
        $this->transactionList = $transactionList ?? [];
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

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUuid(): ?string { return $this->uuid; }
    public function setUuid(?string $uuid): void { $this->uuid = $uuid; }

    public function getUserUuid(): ?string { return $this->userUuid; }
    public function setUserUuid(?string $userUuid): void { $this->userUuid = $userUuid; }

    public function getDueDate(): ?string { return $this->dueDate; }
    public function setDueDate(?string $dueDate): void { $this->dueDate = $dueDate; }

    public function getDueValue(): ?int { return $this->dueValue; }
    public function setDueValue(?int $dueValue): void { $this->dueValue = $dueValue; }

    public function getTransactionList(): array { return $this->transactionList; }
    public function setTransactionList(?array $transactionList): void { $this->transactionList = $transactionList ?? []; }

    public function getState(): ?Payment { return $this->state; }

    public function getTotalValue(): int { return $this->dueValue ?? 0; }

    public function getPaidValue(): int
    {
        $sum = 0;
        foreach ($this->transactionList as $transaction) {
            if ($transaction instanceof Payable) {
                $sum += $transaction->getPaymentValue();
            }
        }
        return $sum;
    }

    public function getPaymentValue(): int { return $this->getPaidValue(); }

    public function changeState(Payment $state): void
    {
        $paid = $this->getPaidValue();
        $total = $this->getTotalValue();

        if ($state instanceof PaidInFull) {
            if (!($paid > 0 && $paid === $total)) {
                throw new ValidationException('Installment can only be PaidInFull when sum of transactions equals total.');
            }
        } elseif ($state instanceof PartiallyPaid) {
            if (!($paid > 0 && $paid < $total)) {
                throw new ValidationException('Installment can only be PartiallyPaid when sum of transactions is greater than zero and less than total.');
            }
        } elseif ($state instanceof Pending) {
            if ($paid !== 0) {
                throw new ValidationException('Installment can only be Pending when sum of transactions is zero.');
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
