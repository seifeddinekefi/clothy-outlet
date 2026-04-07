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
     * Send order confirmation email.
     *
     * @param string $to            Recipient email
     * @param object $order         Order object with details
     * @param array  $orderItems    Array of order items
     * @param string|null $trackingUrl  Guest tracking URL (optional)
     * @return bool
     */
    public function sendOrderConfirmation(string $to, object $order, array $orderItems, ?string $trackingUrl = null): bool
    {
        $orderNumber = str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $subject = "Order Confirmed #{$orderNumber} - " . APP_NAME;

        $body = $this->buildOrderConfirmationEmail($order, $orderItems, $trackingUrl);

        return $this->send($to, $subject, $body);
    }

    /**
     * Send welcome email to new users.
     *
     * @param string $to        Recipient email
     * @param string $userName  User's name
     * @return bool
     */
    public function sendWelcome(string $to, string $userName = 'Customer'): bool
    {
        $subject = 'Welcome to ' . APP_NAME . '!';

        $body = $this->buildWelcomeEmail($userName);

        return $this->send($to, $subject, $body);
    }

    /**
     * Send order shipped notification.
     *
     * @param string $to            Recipient email
     * @param object $order         Order object
     * @param string|null $trackingUrl  Guest tracking URL (optional)
     * @return bool
     */
    public function sendOrderShipped(string $to, object $order, ?string $trackingUrl = null): bool
    {
        $orderNumber = str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $subject = "Your Order #{$orderNumber} Has Been Shipped! - " . APP_NAME;

        $body = $this->buildOrderShippedEmail($order, $trackingUrl);

        return $this->send($to, $subject, $body);
    }

    /**
     * Send order delivered notification.
     *
     * @param string $to            Recipient email
     * @param object $order         Order object
     * @return bool
     */
    public function sendOrderDelivered(string $to, object $order): bool
    {
        $orderNumber = str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $subject = "Your Order #{$orderNumber} Has Been Delivered! - " . APP_NAME;

        $body = $this->buildOrderDeliveredEmail($order);

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
     * Build the order confirmation email HTML.
     */
    private function buildOrderConfirmationEmail(object $order, array $orderItems, ?string $trackingUrl): string
    {
        $appName = APP_NAME;
        $orderNumber = str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $orderDate = date('F j, Y', strtotime($order->created_at));
        $customerName = $order->customer_name ?? 'Customer';
        $firstName = explode(' ', $customerName)[0];

        // Build order items HTML
        $itemsHtml = '';
        foreach ($orderItems as $item) {
            $itemName = htmlspecialchars($item->product_name ?? 'Product');
            $itemQty = (int) $item->quantity;
            $itemSize = !empty($item->size) ? " (Size: {$item->size})" : '';
            $itemPrice = number_format((float) $item->price * $itemQty, 2);
            
            $itemsHtml .= <<<ITEM
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                    <strong>{$itemName}</strong>{$itemSize}<br>
                    <span style="color: #6b7280; font-size: 13px;">Qty: {$itemQty}</span>
                </td>
                <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right; white-space: nowrap;">
                    {$itemPrice} TND
                </td>
            </tr>
ITEM;
        }

        // Format totals
        $subtotal = number_format((float) ($order->subtotal ?? 0), 2);
        $shipping = (float) ($order->shipping_fee ?? 0) === 0.0 ? 'Free' : number_format((float) $order->shipping_fee, 2) . ' TND';
        $discount = (float) ($order->discount ?? 0) > 0 ? '-' . number_format((float) $order->discount, 2) . ' TND' : '';
        $total = number_format((float) ($order->total_price ?? 0), 2);

        // Discount row
        $discountRow = '';
        if ((float) ($order->discount ?? 0) > 0) {
            $discountRow = <<<DISC
            <tr>
                <td style="padding: 8px 12px; color: #059669;">Discount</td>
                <td style="padding: 8px 12px; text-align: right; color: #059669;">{$discount}</td>
            </tr>
DISC;
        }

        // Shipping address
        $address = htmlspecialchars($order->customer_address ?? '');
        $city = htmlspecialchars($order->customer_city ?? '');
        $phone = htmlspecialchars($order->customer_phone ?? '');

        // Tracking section for guests
        $trackingSection = '';
        if ($trackingUrl) {
            $trackingSection = <<<TRACK
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 15px; margin: 20px 0; text-align: center;">
                <p style="margin: 0 0 10px; color: #065f46; font-weight: 600;">📦 Track Your Order</p>
                <p style="margin: 0; font-size: 13px;">
                    <a href="{$trackingUrl}" style="color: #059669; word-break: break-all;">{$trackingUrl}</a>
                </p>
            </div>
TRACK;
        }

        // Payment method
        $paymentMethod = ucwords(str_replace('_', ' ', $order->payment_method ?? 'cash on delivery'));

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f3f4f6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #0a0a0a; color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">{$appName}</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none;">
            <!-- Success Icon -->
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="display: inline-block; width: 60px; height: 60px; background: #ecfdf5; border-radius: 50%; line-height: 60px;">
                    <span style="font-size: 30px;">✓</span>
                </div>
            </div>
            
            <h2 style="text-align: center; color: #059669; margin: 0 0 10px;">Order Confirmed!</h2>
            <p style="text-align: center; color: #6b7280; margin: 0 0 25px;">Thank you for your order, {$firstName}!</p>
            
            <!-- Order Info -->
            <div style="background: #f9fafb; border-radius: 8px; padding: 15px; margin-bottom: 25px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280;">Order Number:</td>
                        <td style="padding: 5px 0; text-align: right; font-weight: bold;">#{$orderNumber}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280;">Order Date:</td>
                        <td style="padding: 5px 0; text-align: right;">{$orderDate}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #6b7280;">Payment Method:</td>
                        <td style="padding: 5px 0; text-align: right;">{$paymentMethod}</td>
                    </tr>
                </table>
            </div>
            
            {$trackingSection}
            
            <!-- Order Items -->
            <h3 style="margin: 25px 0 15px; font-size: 16px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">Order Details</h3>
            <table style="width: 100%; border-collapse: collapse;">
                {$itemsHtml}
            </table>
            
            <!-- Totals -->
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px; background: #f9fafb; border-radius: 8px;">
                <tr>
                    <td style="padding: 8px 12px; color: #6b7280;">Subtotal</td>
                    <td style="padding: 8px 12px; text-align: right;">{$subtotal} TND</td>
                </tr>
                {$discountRow}
                <tr>
                    <td style="padding: 8px 12px; color: #6b7280;">Shipping</td>
                    <td style="padding: 8px 12px; text-align: right;">{$shipping}</td>
                </tr>
                <tr style="border-top: 2px solid #e5e7eb;">
                    <td style="padding: 12px; font-weight: bold; font-size: 16px;">Total</td>
                    <td style="padding: 12px; text-align: right; font-weight: bold; font-size: 16px;">{$total} TND</td>
                </tr>
            </table>
            
            <!-- Shipping Address -->
            <h3 style="margin: 25px 0 15px; font-size: 16px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">Shipping Address</h3>
            <p style="margin: 0; color: #374151;">
                <strong>{$customerName}</strong><br>
                {$address}<br>
                {$city}<br>
                {$phone}
            </p>
            
            <!-- Note -->
            <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 15px; margin-top: 25px;">
                <p style="margin: 0; color: #92400e; font-size: 14px;">
                    <strong>📦 Cash on Delivery:</strong> Please have the exact amount ready ({$total} TND). You can inspect the package before payment.
                </p>
            </div>
        </div>
        <div style="padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
            <p style="margin: 0 0 5px;">&copy; {$appName}. All rights reserved.</p>
            <p style="margin: 0;">This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build the welcome email HTML.
     */
    private function buildWelcomeEmail(string $userName): string
    {
        $appName = APP_NAME;
        $firstName = explode(' ', $userName)[0];
        $shopUrl = url('products');
        $accountUrl = url('account');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to {$appName}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f3f4f6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #0a0a0a; color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">{$appName}</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none;">
            <h2 style="text-align: center; margin: 0 0 20px;">Welcome, {$firstName}! 🎉</h2>
            
            <p>Thank you for creating an account with {$appName}. We're excited to have you!</p>
            
            <p>With your new account, you can:</p>
            <ul style="color: #374151;">
                <li>Track your orders in real-time</li>
                <li>Save your shipping information for faster checkout</li>
                <li>Create a wishlist of your favorite items</li>
                <li>View your order history anytime</li>
            </ul>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{$shopUrl}" style="display: inline-block; padding: 14px 35px; background: #0a0a0a; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">Start Shopping</a>
            </div>
            
            <p style="color: #6b7280; font-size: 14px;">
                You can access your account dashboard anytime at:<br>
                <a href="{$accountUrl}" style="color: #0a0a0a;">{$accountUrl}</a>
            </p>
        </div>
        <div style="padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
            <p style="margin: 0 0 5px;">&copy; {$appName}. All rights reserved.</p>
            <p style="margin: 0;">This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build the order shipped email HTML.
     */
    private function buildOrderShippedEmail(object $order, ?string $trackingUrl): string
    {
        $appName = APP_NAME;
        $orderNumber = str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $customerName = $order->customer_name ?? 'Customer';
        $firstName = explode(' ', $customerName)[0];
        $address = htmlspecialchars($order->customer_address ?? '');
        $city = htmlspecialchars($order->customer_city ?? '');

        $trackingSection = '';
        if ($trackingUrl) {
            $trackingSection = <<<TRACK
            <p style="text-align: center;">
                <a href="{$trackingUrl}" style="display: inline-block; padding: 14px 35px; background: #0a0a0a; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">Track Your Order</a>
            </p>
TRACK;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Order Has Been Shipped</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f3f4f6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #0a0a0a; color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">{$appName}</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none;">
            <!-- Truck Icon -->
            <div style="text-align: center; margin-bottom: 20px;">
                <span style="font-size: 50px;">🚚</span>
            </div>
            
            <h2 style="text-align: center; color: #2563eb; margin: 0 0 10px;">Your Order is On Its Way!</h2>
            <p style="text-align: center; color: #6b7280; margin: 0 0 25px;">Order #{$orderNumber}</p>
            
            <p>Hi {$firstName},</p>
            <p>Great news! Your order has been shipped and is on its way to you.</p>
            
            <div style="background: #f9fafb; border-radius: 8px; padding: 15px; margin: 20px 0;">
                <p style="margin: 0 0 5px; font-weight: bold;">Shipping to:</p>
                <p style="margin: 0; color: #6b7280;">
                    {$customerName}<br>
                    {$address}<br>
                    {$city}
                </p>
            </div>
            
            {$trackingSection}
            
            <p style="color: #6b7280; font-size: 14px; margin-top: 25px;">
                Please ensure someone is available to receive the package. Remember, you can inspect the package before payment.
            </p>
        </div>
        <div style="padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
            <p style="margin: 0 0 5px;">&copy; {$appName}. All rights reserved.</p>
            <p style="margin: 0;">This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build the order delivered email HTML.
     */
    private function buildOrderDeliveredEmail(object $order): string
    {
        $appName = APP_NAME;
        $orderNumber = str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
        $customerName = $order->customer_name ?? 'Customer';
        $firstName = explode(' ', $customerName)[0];
        $shopUrl = url('products');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Order Has Been Delivered</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f3f4f6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #0a0a0a; color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">{$appName}</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none;">
            <!-- Check Icon -->
            <div style="text-align: center; margin-bottom: 20px;">
                <span style="font-size: 50px;">🎉</span>
            </div>
            
            <h2 style="text-align: center; color: #059669; margin: 0 0 10px;">Order Delivered!</h2>
            <p style="text-align: center; color: #6b7280; margin: 0 0 25px;">Order #{$orderNumber}</p>
            
            <p>Hi {$firstName},</p>
            <p>Your order has been successfully delivered. We hope you love your purchase!</p>
            
            <div style="background: #ecfdf5; border-radius: 8px; padding: 20px; margin: 25px 0; text-align: center;">
                <p style="margin: 0; font-size: 16px; color: #065f46;">Thank you for shopping with us! ❤️</p>
            </div>
            
            <p>If you have any questions or concerns about your order, please don't hesitate to contact us.</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{$shopUrl}" style="display: inline-block; padding: 14px 35px; background: #0a0a0a; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">Shop Again</a>
            </div>
        </div>
        <div style="padding: 20px; text-align: center; font-size: 12px; color: #6b7280;">
            <p style="margin: 0 0 5px;">&copy; {$appName}. All rights reserved.</p>
            <p style="margin: 0;">This is an automated message, please do not reply.</p>
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
