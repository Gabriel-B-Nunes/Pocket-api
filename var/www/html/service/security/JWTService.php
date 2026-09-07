<?php
namespace App\service\security;

use App\exception\UnauthorizedException;
use App\service\RedisService;
use DateTime;

require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

Class JWTService {
    const ACCESS_TOKEN_EXP = 3600;
    const REFRESH_TOKEN_EXP = 604800;

    public static function createToken(string $uuid): array {
        $header = base64_encode(json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]));

        $payload = base64_encode(json_encode([
            "sub" => $uuid,
            "exp" => new DateTime()->modify("+1 hour")
        ]));

        $secret = getenv("JWT_SECRET");

        $signature = base64_encode(hash_hmac("sha256", "$header.$payload", $secret, true));

        $accessToken = $header . "." . $payload . "." . $signature;

        $payload = base64_encode(json_encode([
            "sub" => $uuid,
            "exp" => new DateTime()->modify("+7 day")
        ]));

        $signature = base64_encode(hash_hmac("sha256", "$header.$payload", $secret, true));

        $refreshToken = $header . "." . $payload . "." . $signature;

        $redis = RedisService::getInstance();

        $sessionData = $redis->get("user:session:".$uuid);
        if ($sessionData) {
            $tokens = json_decode($sessionData, true);

            $redis->unlink([
                "token:accessToken:".$tokens["accessToken"],
                "token:refreshToken:".$tokens["refreshToken"],
                "user:session:".$uuid
            ]);
        }

        $pipe = $redis->pipeline();
        $pipe->set("token:accessToken:".$accessToken, $uuid, ["ex" => self::ACCESS_TOKEN_EXP]);
        $pipe->set("token:refreshToken:".$refreshToken, $uuid, ["ex" => self::REFRESH_TOKEN_EXP]);
        $pipe->set("user:session:".$uuid, json_encode(["accessToken" => $accessToken, "refreshToken" => $refreshToken]), ["ex" => self::REFRESH_TOKEN_EXP]);
        $pipe->exec();

        $redis->set("token:accessToken:".$uuid, ["ex" => self::ACCESS_TOKEN_EXP]);
        $redis->set("token:refreshToken:".$uuid, ["ex" => self::REFRESH_TOKEN_EXP]);

        return ["accessToken" => $accessToken, "refreshToken" => $refreshToken];
    }

    public static function validate(string $jwtToken): bool {
        $explodedJwtToken = explode(".", $jwtToken);
        $header = $explodedJwtToken[0] ?? "";
        $payload = $explodedJwtToken[1] ?? "";
        $secret = getenv("JWT_SECRET");
        $signature = base64_encode(hash_hmac("sha256", "$header.$payload", $secret, true));
        $expectedToken = $header . "." . $payload . "." . $signature;
        $tokenValidation = hash_equals($expectedToken, $jwtToken);

        if ($tokenValidation) {
            $uuid = json_decode(base64_decode($payload), true)["sub"];
            $redis = RedisService::getInstance();
            $sessionData = json_decode($redis->get("user:session:".$uuid), true);
            if ($sessionData["accessToken"] && $sessionData["accessToken"] === $jwtToken) {
                return true;
            }

            throw new UnauthorizedException("Token expired.");
        }
        
        throw new UnauthorizedException("Invalid token.");
    }

    public static function refresh(string $jwtToken): array {
        
    }
}