<?php declare(strict_types=1);

namespace Tests;

use App\model\Pending;
use App\model\Transaction;
use App\exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class PendingTest extends TestCase
{
    public function testGetPayable(): void
    {
        $t = new Transaction(null, null, null, null, null, 0);
        $p = new Pending($t);
        $this->assertSame($t, $p->getPayable());
    }

    public function testRemovePaymentKeepsPending(): void
    {
        $t = new Transaction(null, null, null, null, null, 0);
        $p = new Pending($t);
        $p->removePayment();
        $this->assertInstanceOf(Pending::class, $t->getState());
    }

    public function testPayInFullThrowsWhenZero(): void
    {
        $t = new Transaction(null, null, null, null, null, 0);
        $p = new Pending($t);
        $this->expectException(ValidationException::class);
        $p->payInFull();
    }
}
