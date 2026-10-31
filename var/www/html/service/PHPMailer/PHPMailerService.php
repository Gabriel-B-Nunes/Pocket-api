<?php

declare(strict_types=1);

namespace App\service\PHPMailer;

use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerService
{
    private static ?PHPMailer $instance = null;

    private function __construct()
    {
        throw new \Exception('Not implemented');
    }

    private function __clone()
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Returns a shared, configured PHPMailer instance (singleton).
     */
    public static function getInstance(): PHPMailer
    {
        if (self::$instance === null) {
            self::$instance = self::createMailer();
        }

        return self::$instance;
    }

    /**
     * Creates a brand-new PHPMailer instance configured from environment variables.
     */
    public static function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);

        $host = self::envValue(['MAIL_HOST', 'SMTP_HOST', 'MAILER_HOST']);

        if ($host !== '') {
            $mail->isSMTP();
            $mail->Host = $host;

            $port = self::envValue(['MAIL_PORT', 'SMTP_PORT', 'MAILER_PORT']);
            $mail->Port = $port !== '' ? (int) $port : 587;

            $username = self::envValue(['MAIL_USERNAME', 'SMTP_USERNAME', 'MAIL_USER', 'SMTP_USER', 'MAILER_USERNAME']);
            $appPassword = self::envValue(['MAIL_APP_PASSWORD', 'SMTP_APP_PASSWORD', 'GOOGLE_APP_PASSWORD', 'APP_PASSWORD']);
            $password = $appPassword !== ''
                ? $appPassword
                : self::envValue(['MAIL_PASSWORD', 'SMTP_PASSWORD', 'MAIL_PASS', 'SMTP_PASS', 'MAILER_PASSWORD']);

            $mail->SMTPAuth = $username !== '' || $password !== '';
            $mail->Username = $username;
            $mail->Password = $password;

            $encryption = self::envValue(['MAIL_ENCRYPTION', 'SMTP_SECURE', 'MAIL_SMTP_SECURE', 'SMTP_ENCRYPTION', 'MAIL_SECURE']);
            $isGoogleSmtp = str_contains(strtolower($host), 'gmail') || str_contains(strtolower($host), 'google');

            if ($encryption === '' && $isGoogleSmtp) {
                $encryption = 'tls';
            }

            if ($encryption !== '') {
                $mail->SMTPSecure = $encryption;
            }

            $debug = self::envValue(['MAIL_DEBUG', 'SMTP_DEBUG']);
            if ($debug !== '') {
                $mail->SMTPDebug = (int) $debug;
            }
        }

        $fromAddress = self::envValue(['MAIL_FROM_ADDRESS', 'MAIL_FROM', 'SMTP_FROM', 'MAILER_FROM', 'FROM_ADDRESS', 'FROM_EMAIL']);
        $fromName = self::envValue(['MAIL_FROM_NAME', 'SMTP_FROM_NAME', 'MAILER_FROM_NAME', 'FROM_NAME']);

        if ($fromAddress !== '') {
            $mail->setFrom($fromAddress, $fromName);
        }

        $charset = self::envValue(['MAIL_CHARSET', 'SMTP_CHARSET', 'CHARSET']);
        if ($charset !== '') {
            $mail->CharSet = $charset;
        } else {
            $mail->CharSet = 'UTF-8';
        }

        return $mail;
    }

    /**
     * Clears the cached singleton instance (mainly useful in tests).
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Returns the first non-empty value found among the given environment variable names.
     */
    private static function envValue(array $keys, string $default = ''): string
    {
        foreach ($keys as $key) {
            $value = getenv($key);

            if ($value !== false && $value !== '') {
                return $value;
            }
        }

        return $default;
    }
}
