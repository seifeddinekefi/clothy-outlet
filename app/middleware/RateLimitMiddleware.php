<?php

/**
 * ============================================================
 * app/middleware/RateLimitMiddleware.php
 * ============================================================
 * Protects against brute-force attacks by limiting login attempts.
 *
 * Features:
 *  - Tracks failed attempts per IP address
 *  - Configurable attempt limits and lockout duration
 *  - Exponential backoff not implemented (simpler lockout)
 *  - Supports both customer and admin login endpoints
 * ============================================================
 */

class RateLimitMiddleware extends Middleware
{
    private int $maxAttempts;
    private int $lockoutMinutes;
    private string $type;

    /**
     * @param string $type         'login' or 'password_reset'
     * @param int    $maxAttempts  Maximum attempts before lockout
     * @param int    $lockoutMins  Lockout duration in minutes
     */
    public function __construct(string $type = 'login', int $maxAttempts = 0, int $lockoutMins = 0)
    {
        $this->type = $type;

        if ($type === 'password_reset') {
            $this->maxAttempts = $maxAttempts ?: EnvLoader::getInt('RATE_LIMIT_RESET_ATTEMPTS', 3);
            $this->lockoutMinutes = $lockoutMins ?: EnvLoader::getInt('RATE_LIMIT_RESET_WINDOW_MINUTES', 60);
        } else {
            $this->maxAttempts = $maxAttempts ?: EnvLoader::getInt('RATE_LIMIT_LOGIN_ATTEMPTS', 5);
            $this->lockoutMinutes = $lockoutMins ?: EnvLoader::getInt('RATE_LIMIT_LOCKOUT_MINUTES', 15);
        }
    }

    /**
     * Handle rate limit check before proceeding.
     */
    public function handle(callable $next): mixed
    {
        $ip = $this->getClientIp();
        $key = $this->getStorageKey($ip);

        // Check if currently locked out
        if ($this->isLockedOut($key)) {
            $remainingTime = $this->getRemainingLockoutTime($key);
            $this->respondWithLockout($remainingTime);
            return null;
        }

        return $next();
    }

    /**
     * Record a failed attempt (call this after failed login/reset).
     */
    public static function recordFailedAttempt(string $type = 'login'): void
    {
        $instance = new self($type);
        $ip = $instance->getClientIp();
        $key = $instance->getStorageKey($ip);

        $data = Session::get($key, [
            'attempts' => 0,
            'first_attempt' => time(),
            'locked_until' => null,
        ]);

        $data['attempts']++;
        $data['last_attempt'] = time();

        // Lock out if max attempts exceeded
        if ($data['attempts'] >= $instance->maxAttempts) {
            $data['locked_until'] = time() + ($instance->lockoutMinutes * 60);
        }

        Session::set($key, $data);
    }

    /**
     * Clear attempts after successful login (call after successful auth).
     */
    public static function clearAttempts(string $type = 'login'): void
    {
        $instance = new self($type);
        $ip = $instance->getClientIp();
        $key = $instance->getStorageKey($ip);

        Session::delete($key);
    }

    /**
     * Check remaining attempts before lockout.
     */
    public static function getRemainingAttempts(string $type = 'login'): int
    {
        $instance = new self($type);
        $ip = $instance->getClientIp();
        $key = $instance->getStorageKey($ip);

        $data = Session::get($key, ['attempts' => 0]);

        return max(0, $instance->maxAttempts - $data['attempts']);
    }

    /**
     * Check if an IP is currently locked out.
     */
    private function isLockedOut(string $key): bool
    {
        $data = Session::get($key);

        if (!$data || !isset($data['locked_until'])) {
            return false;
        }

        if ($data['locked_until'] && time() < $data['locked_until']) {
            return true;
        }

        // Lockout expired, clear the record
        if ($data['locked_until'] && time() >= $data['locked_until']) {
            Session::delete($key);
        }

        return false;
    }

    /**
     * Get remaining lockout time in seconds.
     */
    private function getRemainingLockoutTime(string $key): int
    {
        $data = Session::get($key);

        if (!$data || !isset($data['locked_until'])) {
            return 0;
        }

        return max(0, $data['locked_until'] - time());
    }

    /**
     * Respond with lockout error message.
     */
    private function respondWithLockout(int $remainingSeconds): void
    {
        $minutes = ceil($remainingSeconds / 60);
        $message = "Too many attempts. Please try again in {$minutes} minute(s).";

        Session::flash('error', $message);

        // Determine redirect based on type and current URL
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '';

        if (str_contains($currentUrl, 'admin')) {
            redirect(url('admin/login'));
        } elseif ($this->type === 'password_reset') {
            redirect(url('forgot-password'));
        } else {
            redirect(url('login'));
        }
    }

    /**
     * Get storage key for rate limit data.
     */
    private function getStorageKey(string $ip): string
    {
        return "_rate_limit_{$this->type}_{$ip}";
    }

    /**
     * Get client IP address.
     */
    private function getClientIp(): string
    {
        // Check for proxy headers
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // General proxy
            'HTTP_X_REAL_IP',            // Nginx proxy
            'REMOTE_ADDR',               // Direct connection
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);

                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }
}
