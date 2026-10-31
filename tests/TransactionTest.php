<?php declare(strict_types=1);

namespace Tests;

use App\model\Transaction;
use App\model\Pending;
use App\model\PaidInFull;
use App\exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $t = new Transaction(1, 'uuid-1', 'user-1', '2024-01-01', 100);
        $this->assertSame(1, $t->getId());
        $this->assertSame('uuid-1', $t->getUuid());
        $this->assertSame('user-1', $t->getUserUuid());
        $this->assertSame('2024-01-01', $t->getPaymentDate());
        $this->assertSame(100, $t->getPaymentValue());
        $this->assertInstanceOf(PaidInFull::class, $t->getState());
    }

    public function testPendingWhenZero(): void
    {
        $t = new Transaction(null, null, null, null, 0);
        $this->assertInstanceOf(Pending::class, $t->getState());
    }

    public function testSetters(): void
    {
        $t = new Transaction();
        $t->setId(2);
        $t->setUuid('u');
        $t->setUserUuid('uu');
        $t->setPaymentDate('d');
        $t->setPaymentValue(50);
        $this->assertSame(2, $t->getId());
        $this->assertSame('u', $t->getUuid());
        $this->assertSame('uu', $t->getUserUuid());
        $this->assertSame('d', $t->getPaymentDate());
        $this->assertSame(50, $t->getPaymentValue());
    }

    public function testPartiallyPaidThrows(): void
    {
        $t = new Transaction(null, null, null, null, 100);
        $this->expectException(ValidationException::class);
        $t->payPartially();
    }

    public function testRemovePaymentWhenZeroAllowed(): void
    {
        $t = new Transaction(null, null, null, null, 0);
        $t->removePayment();
        $this->assertInstanceOf(Pending::class, $t->getState());
    }
}
