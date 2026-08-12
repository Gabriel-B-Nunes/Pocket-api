<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

$format = "Y-m-d";
$datetime = "2026-08-01";
$test = DateTime::createFromFormat("Y-m-d\TH:i", $datetime) ?: DateTime::createFromFormat("Y-m-d", $datetime);
print_r($test->format($format));