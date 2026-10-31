<?php declare(strict_types=1);

namespace Tests;

use App\model\Movement;
use App\model\Installment;
use App\model\Transaction;
use App\model\MovementItem;
use App\model\Pending;
use App\model\PaidInFull;
use App\model\PartiallyPaid;
use App\exception\ValidationException;
use App\dto\movementItem\MovementItemCreateDTO;
use PHPUnit\Framework\TestCase;

final class MovementTest extends TestCase
{
    public function testMovementWithItemsTotal(): void
    {
        $dto = new MovementItemCreateDTO('user-1', 'item-1', 10, 2, 'UN', 'BRL');
        $item = MovementItem::createConstructor($dto);
        $m = new Movement(null, null, null, 'desc', null, [$item], []);
        $this->assertSame(20, $m->getTotalValue());
        $this->assertSame(0, $m->getPaymentValue());
        $this->assertInstanceOf(Pending::class, $m->getState());
    }

    public function testMovementWithInstallmentsPartiallyPaid(): void
    {
        $t = new Transaction(null, null, null, null, 40);
        $inst = new Installment(null, null, null, null, 100, [$t]);
        $m = new Movement(null, null, null, 'desc', null, [], [$inst]);
        $this->assertSame(100, $m->getTotalValue());
        $this->assertSame(40, $m->getPaymentValue());
        $this->assertInstanceOf(PartiallyPaid::class, $m->getState());
    }

    public function testMovementPaidInFull(): void
    {
        $t = new Transaction(null, null, null, null, 100);
        $inst = new Installment(null, null, null, null, 100, [$t]);
        $m = new Movement(null, null, null, 'desc', null, [], [$inst]);
        $this->assertInstanceOf(PaidInFull::class, $m->getState());
    }

    public function testMovementPaidInFullThrows(): void
    {
        $t = new Transaction(null, null, null, null, 40);
        $inst = new Installment(null, null, null, null, 100, [$t]);
        $m = new Movement(null, null, null, 'desc', null, [], [$inst]);
        $this->expectException(ValidationException::class);
        $m->payInFull();
    }

    public function testSetters(): void
    {
        $m = new Movement();
        $m->setId(1);
        $m->setUuid('u');
        $m->setUserUuid('uu');
        $m->setDescription('d');
        $m->setTransactionDate('td');
        $m->setMovementItemList([]);
        $m->setInstallmentList([]);
        $m->setFinancialType('CREDIT');
        $m->setRecurrence('MONTHLY');
        $this->assertSame(1, $m->getId());
        $this->assertSame('u', $m->getUuid());
        $this->assertSame('uu', $m->getUserUuid());
        $this->assertSame('d', $m->getDescription());
        $this->assertSame('td', $m->getTransactionDate());
        $this->assertSame([], $m->getMovementItemList());
        $this->assertSame([], $m->getInstallmentList());
        $this->assertSame('CREDIT', $m->getFinancialType());
        $this->assertSame('MONTHLY', $m->getRecurrence());
    }
}
