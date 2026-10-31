<?php declare(strict_types=1);

namespace Tests;

use App\model\PartiallyPaid;
use App\model\Installment;
use App\model\Transaction;
use App\exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class PartiallyPaidTest extends TestCase
{
    private function installment(): Installment
    {
        $t = new Transaction(null, null, null, null, 40);
        return new Installment(null, null, null, null, 100, [$t]);
    }

    public function testGetPayable(): void
    {
        $i = $this->installment();
        $p = new PartiallyPaid($i);
        $this->assertSame($i, $p->getPayable());
    }

    public function testPayPartiallyKeepsPartiallyPaid(): void
    {
        $i = $this->installment();
        $p = new PartiallyPaid($i);
        $p->payPartially();
        $this->assertInstanceOf(PartiallyPaid::class, $i->getState());
    }

    public function testPayInFullThrowsWhenPartial(): void
    {
        $i = $this->installment();
        $p = new PartiallyPaid($i);
        $this->expectException(ValidationException::class);
        $p->payInFull();
    }
}
