<?php declare(strict_types=1);

namespace Tests;

use App\model\Payment;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class PaymentTest extends TestCase
{
    public function testInterfaceHasRequiredMethods(): void
    {
        $reflection = new ReflectionClass(Payment::class);
        $this->assertTrue($reflection->isInterface());
        $this->assertTrue($reflection->hasMethod('payInFull'));
        $this->assertTrue($reflection->hasMethod('payPartially'));
        $this->assertTrue($reflection->hasMethod('removePayment'));
    }
}
