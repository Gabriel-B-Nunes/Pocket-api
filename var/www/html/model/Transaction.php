<?php

declare(strict_types=1);

namespace App\model;

use App\exception\ValidationException;

class Transaction implements Payable, Payment
{
    private ?Payment $state = null;

    public function __construct(
        private ?int $id = null,
        private ?string $uuid = null,
        private ?string $userUuid = null,
        private ?string $paymentDate = null,
        private ?int $paymentValue = null
    ) {
        $this->state = $this->paymentValue > 0
            ? new PaidInFull($this)
            : new Pending($this);
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUuid(): ?string { return $this->uuid; }
    public function setUuid(?string $uuid): void { $this->uuid = $uuid; }

    public function getUserUuid(): ?string { return $this->userUuid; }
    public function setUserUuid(?string $userUuid): void { $this->userUuid = $userUuid; }

    public function getPaymentDate(): ?string { return $this->paymentDate; }
    public function setPaymentDate(?string $paymentDate): void { $this->paymentDate = $paymentDate; }

    public function getPaymentValue(): int { return $this->paymentValue ?? 0; }
    public function setPaymentValue(?int $paymentValue): void { $this->paymentValue = $paymentValue; }

    public function getState(): ?Payment { return $this->state; }

    public function getTotalValue(): int { return $this->paymentValue ?? 0; }

    public function getPaidValue(): int { return $this->paymentValue ?? 0; }

    public function changeState(Payment $state): void
    {
        $paid = $this->getPaidValue();
        $total = $this->getTotalValue();

        if ($state instanceof PaidInFull) {
            if (!($paid > 0 && $paid === $total)) {
                throw new ValidationException('Transaction can only be PaidInFull when paid value equals total.');
            }
        } elseif ($state instanceof PartiallyPaid) {
            if (!($paid > 0 && $paid < $total)) {
                throw new ValidationException('Transaction can only be PartiallyPaid when paid value is greater than zero and less than total.');
            }
        } elseif ($state instanceof Pending) {
            if ($paid !== 0) {
                throw new ValidationException('Transaction can only be Pending when paid value is zero.');
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
