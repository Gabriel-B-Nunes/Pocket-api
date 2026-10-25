<?php

declare(strict_types=1);

$documentRoot = __DIR__ . '/../var/www/html';
$_SERVER['DOCUMENT_ROOT'] = $documentRoot;

require_once $documentRoot . '/autoload.php';
