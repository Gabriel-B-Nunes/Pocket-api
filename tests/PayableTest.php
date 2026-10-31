<?php declare(strict_types=1);

namespace Tests;

use App\model\Payable;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class PayableTest extends TestCase
{
    public function testInterfaceHasRequiredMethods(): void
    {
        $reflection = new ReflectionClass(Payable::class);
        $this->assertTrue($reflection->isInterface());
        $this->assertTrue($reflection->hasMethod('getPaymentValue'));
    }
}
