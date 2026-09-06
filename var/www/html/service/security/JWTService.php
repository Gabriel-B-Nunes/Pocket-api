<?php
namespace App\service\security;

use DateTime;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

Class JWTService {
    public static function create(string $uuid): string {
        $header = base64_encode(json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]));

        $payload = base64_encode(json_encode([
            "sub" => $uuid,
            "exp" => new DateTime()->modify("+1 hour")->getTimestamp()
        ]));

        $secret = getenv("JWT_SECRET");

        $signature = base64_encode(hash_hmac("sha256", "$header.$payload", $secret, true));

        $jwtToken = $header . "." . $payload . "." . $signature;

        return $jwtToken;
    }

    public static function validate(string $jwtToken): bool {
        $explodedJwtToken = explode(".", $jwtToken);

        $header = $explodedJwtToken[0] ?? "";
        $payload = $explodedJwtToken[1] ?? "";
        
        $secret = getenv("JWT_SECRET");

        $signature = hash_hmac("sha256", "$header.$payload", $secret, true);

        $expectedToken = $header . "." . $payload . "." . $signature;

        return hash_equals($expectedToken, $jwtToken);
    }
}