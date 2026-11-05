<?php

declare(strict_types=1);

namespace Tests;

use OpenApi\Builder;
use PHPUnit\Framework\TestCase;

class OpenApiSpecTest extends TestCase
{
    public function testOpenApiSpecBuilds(): void
    {
        $openApi = (new Builder())
            ->addSource(__DIR__ . '/../var/www/html')
            ->build();

        $json = $openApi->toJson();
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertSame('Pocket API', $data['info']['title']);
        $this->assertNotEmpty($data['paths']);
        $this->assertArrayHasKey('components', $data);
        $this->assertArrayHasKey('schemas', $data['components']);
    }
}