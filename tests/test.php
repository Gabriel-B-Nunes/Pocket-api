<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

require_once $_SERVER["DOCUMENT_ROOT"] . "/autoload.php";

final class ExampleTest extends TestCase
{
    public function testTrueIsTrue(): void
    {
        $this->assertTrue(true);
    }
}
