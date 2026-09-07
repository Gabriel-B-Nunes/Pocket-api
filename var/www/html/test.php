<?php

use App\service\security\JWTService;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

$validation = JWTService::validate("eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwMWEwNzg4Zi1hN2MzLTdkM2QtOWI3OC1kYmE3ZmY4ZWU4OGQiLCJleHAiOnsiZGF0ZSI6IjIwMjYtMDktMDcgMDI6MTY6NDIuNjQ2MjIzIiwidGltZXpvbmVfdHlwZSI6MywidGltZXpvbmUiOiJVVEMifX0=.p9PaA5WgV0GDuf5UEYfilnf5W4eArpepegHu/pZcIIo=");
var_dump($validation);