<?php

declare(strict_types=1);

namespace App\model;

interface Payable
{
    public function getPaymentValue(): int;
}
