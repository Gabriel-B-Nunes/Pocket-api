<?php declare(strict_types=1);

namespace Tests;

use App\model\Installment;
use App\model\Transaction;
use App\model\Pending;
use App\model\PaidInFull;
use App\model\PartiallyPaid;
use App\exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class InstallmentTest extends TestCase
{
    public function testPendingWhenNoTransactions(): void
    {
        $i = new Installment(null, null, null, null, null, 100, []);
        $this->assertSame(0, $i->getPaymentValue());
        $this->assertSame(100, $i->getTotalValue());
        $this->assertInstanceOf(Pending::class, $i->getState());
    }

    public function testPartiallyPaid(): void
    {
        $t = new Transaction(null, null, null, null, null, 40);
        $i = new Installment(null, null, null, null, null, 100, [$t]);
        $this->assertSame(40, $i->getPaymentValue());
        $this->assertInstanceOf(PartiallyPaid::class, $i->getState());
    }

    public function testPaidInFull(): void
    {
        $t = new Transaction(null, null, null, null, null, 100);
        $i = new Installment(null, null, null, null, null, 100, [$t]);
        $this->assertInstanceOf(PaidInFull::class, $i->getState());
    }

    public function testPaidInFullThrowsWhenNotFull(): void
    {
        $t = new Transaction(null, null, null, null, null, 40);
        $i = new Installment(null, null, null, null, null, 100, [$t]);
        $this->expectException(ValidationException::class);
        $i->payInFull();
    }

    public function testSetters(): void
    {
        $i = new Installment();
        $i->setId(1);
        $i->setUuid('u');
        $i->setUserUuid('uu');
        $i->setDueDate('d');
        $i->setDueValue(50);
        $i->setTransactionList([]);
        $this->assertSame(1, $i->getId());
        $this->assertSame('u', $i->getUuid());
        $this->assertSame('uu', $i->getUserUuid());
        $this->assertSame('d', $i->getDueDate());
        $this->assertSame(50, $i->getDueValue());
        $this->assertSame([], $i->getTransactionList());
    }
}
