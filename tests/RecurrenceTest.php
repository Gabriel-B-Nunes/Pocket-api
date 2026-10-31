<?php declare(strict_types=1);

namespace Tests;

use App\model\Recurrence;
use PHPUnit\Framework\TestCase;

final class RecurrenceTest extends TestCase
{
    public function testCases(): void
    {
        $this->assertSame('DAILY', Recurrence::DAILY->value);
        $this->assertSame('WEEKLY', Recurrence::WEEKLY->value);
        $this->assertSame('MONTHLY', Recurrence::MONTHLY->value);
        $this->assertSame('ANNUALLY', Recurrence::ANNUALLY->value);
    }
}
