<?php

declare(strict_types=1);

namespace Tests;

use App\service\PHPMailer\PHPMailerService;
use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;

final class PHPMailerServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        PHPMailerService::reset();

        foreach ([
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_APP_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_FROM_ADDRESS',
            'MAIL_FROM_NAME',
            'MAIL_CHARSET',
            'MAIL_DEBUG',
        ] as $key) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);
        }
    }

    public function testGetInstanceReturnsConfiguredPhpMailer(): void
    {
        putenv('MAIL_HOST=smtp.example.com');
        putenv('MAIL_PORT=2525');
        putenv('MAIL_USERNAME=user@example.com');
        putenv('MAIL_PASSWORD=secret');
        putenv('MAIL_ENCRYPTION=tls');
        putenv('MAIL_FROM_ADDRESS=no-reply@example.com');
        putenv('MAIL_FROM_NAME=Pocket Test');
        putenv('MAIL_CHARSET=UTF-8');
        putenv('MAIL_DEBUG=0');

        PHPMailerService::reset();

        $mailer = PHPMailerService::getInstance();

        $this->assertInstanceOf(PHPMailer::class, $mailer);
        $this->assertSame('smtp', $mailer->Mailer);
        $this->assertSame('smtp.example.com', $mailer->Host);
        $this->assertSame(2525, $mailer->Port);
        $this->assertSame('user@example.com', $mailer->Username);
        $this->assertSame('secret', $mailer->Password);
        $this->assertTrue($mailer->SMTPAuth);
        $this->assertSame('tls', $mailer->SMTPSecure);
        $this->assertSame('no-reply@example.com', $mailer->From);
        $this->assertSame('Pocket Test', $mailer->FromName);
        $this->assertSame('UTF-8', $mailer->CharSet);
    }

    public function testGetInstanceIsSingleton(): void
    {
        putenv('MAIL_HOST=smtp.example.com');

        PHPMailerService::reset();

        $first = PHPMailerService::getInstance();
        $second = PHPMailerService::getInstance();

        $this->assertSame($first, $second);
    }

    public function testCreateMailerReturnsNewInstance(): void
    {
        putenv('MAIL_HOST=smtp.example.com');

        PHPMailerService::reset();

        $first = PHPMailerService::createMailer();
        $second = PHPMailerService::createMailer();

        $this->assertNotSame($first, $second);
    }

    public function testAppPasswordTakesPriorityAndGoogleDefaultsApply(): void
    {
        putenv('MAIL_HOST=smtp.gmail.com');
        putenv('MAIL_USERNAME=user@gmail.com');
        putenv('MAIL_PASSWORD=regular-password');
        putenv('MAIL_APP_PASSWORD=abcd efgh ijkl mnop');
        putenv('MAIL_PORT=');
        putenv('MAIL_ENCRYPTION=');

        PHPMailerService::reset();

        $mailer = PHPMailerService::getInstance();

        $this->assertSame('user@gmail.com', $mailer->Username);
        $this->assertSame('abcd efgh ijkl mnop', $mailer->Password);
        $this->assertSame('tls', $mailer->SMTPSecure);
        $this->assertSame(587, $mailer->Port);
    }
}
