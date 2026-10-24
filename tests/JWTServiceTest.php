<?php declare(strict_types=1);

namespace Tests;

use App\service\security\JWTService;
use PHPUnit\Framework\TestCase;

final class JWTServiceTest extends TestCase
{
    public function testGetSubjectReturnsEmptyForInvalidToken(): void
    {
        $this->assertSame('', JWTService::getSubject('not-a-jwt'));
    }

    public function testGetSubjectExtractsSubClaim(): void
    {
        $payload = base64_encode(json_encode(['sub' => 'user-uuid-123']));
        $jwt = 'header.' . $payload . '.signature';

        $this->assertSame('user-uuid-123', JWTService::getSubject($jwt));
    }
}
