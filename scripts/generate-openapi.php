<?php

declare(strict_types=1);

require_once __DIR__ . '/../var/www/html/autoload.php';

$builder = new OpenApi\Builder();
$openApi = $builder->addSource([__DIR__ . '/../var/www/html'])->build();

$outputPath = __DIR__ . '/../openapi.json';
$openApi->saveAs($outputPath);

echo "OpenAPI spec written to {$outputPath}\n";