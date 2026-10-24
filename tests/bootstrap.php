<?php

declare(strict_types=1);

$documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
if ($documentRoot === '') {
    $documentRoot = '/var/www/html';
}
$_SERVER['DOCUMENT_ROOT'] = $documentRoot;

require_once $documentRoot . '/autoload.php';
