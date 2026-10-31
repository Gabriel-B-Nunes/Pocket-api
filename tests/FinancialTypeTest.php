<?php declare(strict_types=1);

namespace Tests;

use App\model\FinancialType;
use PHPUnit\Framework\TestCase;

final class FinancialTypeTest extends TestCase
{
    public function testCases(): void
    {
        $this->assertSame('CREDIT', FinancialType::CREDIT->value);
        $this->assertSame('DEBIT', FinancialType::DEBIT->value);
    }
}
