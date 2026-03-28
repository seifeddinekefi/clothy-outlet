<?php

/**
 * ============================================================
 * core/Mailer.php
 * ============================================================
 * Simple email sending class supporting multiple drivers.
 *
 * Drivers:
 *  - 'log': Writes email to storage/logs/mail.log (development)
 *  - 'smtp': Sends via SMTP using PHP's mail() or stream sockets
 *  - 'mail': Uses PHP's native mail() function
 * ============================================================
 */

class Mailer
{
    private string $driver;
    private array $config;

    public function __construct()
    {
        $this->driver = EnvLoader::get('MAIL_DRIVER', 'log');
        $this->config = [
            'host'       => EnvLoader::get('MAIL_HOST', 'localhost'),
            'port'       => EnvLoader::getInt('MAIL_PORT', 587),
            'username'   => EnvLoader::get('MAIL_USERNAME', ''),
            'password'   => EnvLoader::get('MAIL_PASSWORD', ''),
            'encryption' => EnvLoader::get('MAIL_ENCRYPTION', 'tls'),
            'from_address' => EnvLoader::get('MAIL_FROM_ADDRESS', 'noreply@localhost'),
            'from_name'    => EnvLoader::get('MAIL_FROM_NAME', APP_NAME),
        ];
    }

    /**
     * Send an email.
     *
     * @param string $to      Recipient email address
     * @param string $subject Email subject
     * @param string $body    Email body (HTML supported)
     * @param array  $options Additional options (from, replyTo, etc.)
     * @return bool True if email was sent successfully
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        $from = $options['from'] ?? $this->config['from_address'];
        $fromName = $options['from_name'] ?? $this->config['from_name'];

        return match ($this->driver) {
            'log'  => $this->sendViaLog($to, $subject, $body, $from, $fromName),
            'smtp' => $this->sendViaSMTP($to, $subject, $body, $from, $fromName),
            'mail' => $this->sendViaMail($to, $subject, $body, $from, $fromName),
            default => $this->sendViaLog($to, $subject, $body, $from, $fromName),
        };
    }

    /**
     * Send password reset email.
     *
     * @param string $to        Recipient email
     * @param string $resetUrl  Full URL to reset password
     * @param string $userName  User's name for personalization
     * @return bool
     */
    public function sendPasswordReset(string $to, string $resetUrl, string $userName = 'Customer'): bool
    {
        $subject = 'Reset Your Password - ' . APP_NAME;

        $body = $this->buildPasswordResetEmail($resetUrl, $userName);

        return $this->send($to, $subject, $body);
    }

    /**
     * Build the password reset email HTML.
     */
    private function buildPasswordResetEmail(string $resetUrl, string $userName): string
    {
        $appName = APP_NAME;
        $expiryMinutes = 60; // Token expires in 1 hour

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 30px; background: #f9fafb; }
        .button { display: inline-block; padding: 12px 30px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .warning { color: #dc2626; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$appName}</h1>
        </div>
        <div class="content">
            <p>Hello {$userName},</p>
            <p>We received a request to reset your password. Click the button below to create a new password:</p>
            <p style="text-align: center;">
                <a href="{$resetUrl}" class="button">Reset Password</a>
            </p>
            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all; font-size: 13px; color: #666;">{$resetUrl}</p>
            <p class="warning">This link will expire in {$expiryMinutes} minutes.</p>
            <p>If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged.</p>
        </div>
        <div class="footer">
            <p>&copy; {$appName}. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Log email to file (development mode).
     */
    private function sendViaLog(string $to, string $subject, string $body, string $from, string $fromName): bool
    {
        $logDir = BASE_PATH . '/storage/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/mail.log';
        $timestamp = date('Y-m-d H:i:s');
        $separator = str_repeat('=', 60);

        $logEntry = <<<LOG
{$separator}
[{$timestamp}] EMAIL SENT (via log driver)
{$separator}
From: {$fromName} <{$from}>
To: {$to}
Subject: {$subject}
{$separator}
{$body}
{$separator}


LOG;

        return file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) !== false;
    }

    /**
     * Send via PHP mail() function.
     */
    private function sendViaMail(string $to, string $subject, string $body, string $from, string $fromName): bool
    {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            "From: {$fromName} <{$from}>",
            "Reply-To: {$from}",
            'X-Mailer: PHP/' . phpversion(),
        ];

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }

    /**
     * Send via SMTP (basic implementation).
     */
    private function sendViaSMTP(string $to, string $subject, string $body, string $from, string $fromName): bool
    {
        $host = $this->config['host'];
        $port = $this->config['port'];
        $username = $this->config['username'];
        $password = $this->config['password'];
        $encryption = $this->config['encryption'];

        // Use TLS prefix for encrypted connections
        $protocol = ($encryption === 'ssl') ? 'ssl://' : '';
        $hostname = $protocol . $host;

        $socket = @fsockopen($hostname, $port, $errno, $errstr, 30);

        if (!$socket) {
            error_log("SMTP connection failed: {$errstr} ({$errno})");
            // Fallback to log driver
            return $this->sendViaLog($to, $subject, $body, $from, $fromName);
        }

        // Read greeting
        $this->smtpRead($socket);

        // EHLO
        $this->smtpSend($socket, "EHLO " . gethostname());
        $response = $this->smtpRead($socket);

        // STARTTLS for TLS encryption
        if ($encryption === 'tls' && strpos($response, 'STARTTLS') !== false) {
            $this->smtpSend($socket, "STARTTLS");
            $this->smtpRead($socket);

            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            $this->smtpSend($socket, "EHLO " . gethostname());
            $this->smtpRead($socket);
        }

        // AUTH LOGIN
        if ($username && $password) {
            $this->smtpSend($socket, "AUTH LOGIN");
            $this->smtpRead($socket);

            $this->smtpSend($socket, base64_encode($username));
            $this->smtpRead($socket);

            $this->smtpSend($socket, base64_encode($password));
            $this->smtpRead($socket);
        }

        // MAIL FROM
        $this->smtpSend($socket, "MAIL FROM:<{$from}>");
        $this->smtpRead($socket);

        // RCPT TO
        $this->smtpSend($socket, "RCPT TO:<{$to}>");
        $this->smtpRead($socket);

        // DATA
        $this->smtpSend($socket, "DATA");
        $this->smtpRead($socket);

        // Email headers and body
        $headers = "From: {$fromName} <{$from}>\r\n";
        $headers .= "To: {$to}\r\n";
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "\r\n";

        $this->smtpSend($socket, $headers . $body . "\r\n.");
        $this->smtpRead($socket);

        // QUIT
        $this->smtpSend($socket, "QUIT");
        fclose($socket);

        return true;
    }

    private function smtpSend($socket, string $command): void
    {
        fwrite($socket, $command . "\r\n");
    }

    private function smtpRead($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }
}
