<?php

declare(strict_types=1);

namespace App\model;

class PartiallyPaid implements Payment
{
    private Payable $payable;

    public function __construct(Payable $payable)
    {
        $this->payable = $payable;
    }

    public function getPayable(): Payable
    {
        return $this->payable;
    }

    public function setPayable(Payable $payable): void
    {
        $this->payable = $payable;
    }

    public function payInFull(): void
    {
        if ($this->payable instanceof Movement
            || $this->payable instanceof Installment
            || $this->payable instanceof Transaction) {
            $this->payable->changeState(new PaidInFull($this->payable));
        }
    }

    public function payPartially(): void
    {
        if ($this->payable instanceof Movement
            || $this->payable instanceof Installment
            || $this->payable instanceof Transaction) {
            $this->payable->changeState(new PartiallyPaid($this->payable));
        }
    }

    public function removePayment(): void
    {
        if ($this->payable instanceof Movement
            || $this->payable instanceof Installment
            || $this->payable instanceof Transaction) {
            $this->payable->changeState(new Pending($this->payable));
        }
    }
}
