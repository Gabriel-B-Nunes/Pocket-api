<?php

declare(strict_types=1);

namespace App\model;

interface Payment
{
    public function payInFull(): void;

    public function payPartially(): void;

    public function removePayment(): void;
}
