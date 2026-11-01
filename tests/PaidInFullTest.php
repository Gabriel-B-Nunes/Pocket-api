<?php declare(strict_types=1);

namespace Tests;

use App\model\PaidInFull;
use App\model\Transaction;
use App\exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class PaidInFullTest extends TestCase
{
    public function testGetPayable(): void
    {
        $t = new Transaction(null, null, null, null, null, 100);
        $p = new PaidInFull($t);
        $this->assertSame($t, $p->getPayable());
    }

    public function testPayInFullKeepsPaidInFull(): void
    {
        $t = new Transaction(null, null, null, null, null, 100);
        $p = new PaidInFull($t);
        $p->payInFull();
        $this->assertInstanceOf(PaidInFull::class, $t->getState());
    }

    public function testRemovePaymentThrowsWhenPaid(): void
    {
        $t = new Transaction(null, null, null, null, null, 100);
        $p = new PaidInFull($t);
        $this->expectException(ValidationException::class);
        $p->removePayment();
    }
}
