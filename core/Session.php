<?php

/**
 * ============================================================
 * core/Session.php
 * ============================================================
 * Centralised, secure session management.
 *
 * Features:
 *  - Hardened session cookie flags (HttpOnly, SameSite, Secure)
 *  - Session fixation prevention (regenerate ID on auth)
 *  - Flash message support (set + consume in one request cycle)
 *  - Helpers: set, get, has, delete, destroy
 *  - CSRF token storage/retrieval
 * ============================================================
 */

class Session
{
    private static bool $started = false;

    // ── Initialisation ───────────────────────────────────────

    /**
     * Configure and start the session (call once in bootstrap).
     */
    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // ── Hardened cookie parameters ────────────────────
        session_name(SESSION_NAME);

        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'domain'   => '',
            'secure'   => SESSION_SECURE,
            'httponly' => SESSION_HTTPONLY,
            'samesite' => SESSION_SAMESITE,
        ]);

        session_start();
        self::$started = true;

        // ── Initialise internal namespaces ───────────────
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
    }

    /**
     * Regenerate session ID (call after login / privilege change).
     * Prevents session fixation attacks.
     */
    public static function regenerate(bool $deleteOld = true): void
    {
        self::ensureStarted();
        session_regenerate_id($deleteOld);
    }

    // ── Core CRUD ────────────────────────────────────────────

    /**
     * Set a session value.
     *
     * @param  string $key   Dot-notation is NOT supported (keep simple).
     * @param  mixed  $value
     */
    public static function set(string $key, mixed $value): void
    {
        self::ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve a session value.
     *
     * @param  string $key
     * @param  mixed  $default  Returned when key does not exist
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::ensureStarted();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check whether a key exists (and is not null).
     */
    public static function has(string $key): bool
    {
        self::ensureStarted();
        return isset($_SESSION[$key]);
    }

    /**
     * Delete a session key.
     */
    public static function delete(string $key): void
    {
        self::ensureStarted();
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the entire session (logout use-case).
     */
    public static function destroy(): void
    {
        self::ensureStarted();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }

    // ── Flash Messages ───────────────────────────────────────

    /**
     * Store a flash message (persists for exactly one redirect cycle).
     *
     * @param  string $type  e.g. 'success' | 'error' | 'warning' | 'info'
     * @param  string $message
     */
    public static function flash(string $type, string $message): void
    {
        self::ensureStarted();
        $_SESSION['_flash'][$type][] = $message;
    }

    /**
     * Read and consume all flash messages (clears them after reading).
     *
     * @return array<string, string[]>
     */
    public static function getFlash(): array
    {
        self::ensureStarted();
        $flash = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flash;
    }

    /**
     * Check if any flash messages exist for a given type.
     */
    public static function hasFlash(string $type): bool
    {
        self::ensureStarted();
        return !empty($_SESSION['_flash'][$type]);
    }

    // ── Authentication Helpers ────────────────────────────────

    /**
     * Store authenticated user data in session.
     *
     * @param  array<string, mixed> $user  Typically id, name, email, role
     */
    public static function login(array $user): void
    {
        self::ensureStarted();
        self::regenerate();
        self::set('auth_user', $user);
    }

    /**
     * Remove authenticated user from session.
     */
    public static function logout(): void
    {
        self::destroy();
    }

    /**
     * Return the authenticated user array, or null.
     *
     * @return array<string, mixed>|null
     */
    public static function user(): ?array
    {
        return self::get('auth_user');
    }

    /**
     * Check if a user is authenticated.
     */
    public static function isLoggedIn(): bool
    {
        return self::has('auth_user');
    }

    /**
     * Check if the authenticated user carries a given role.
     */
    public static function hasRole(string $role): bool
    {
        $user = self::user();
        return $user !== null && isset($user['role']) && $user['role'] === $role;
    }

    // ── CSRF Token Storage ───────────────────────────────────

    /**
     * Store a CSRF token (used by CsrfMiddleware / helpers.php).
     */
    public static function setCsrfToken(string $token): void
    {
        self::set(CSRF_TOKEN_NAME, [
            'token'   => $token,
            'expires' => time() + CSRF_TOKEN_TTL,
        ]);
    }

    /**
     * Retrieve stored CSRF token data.
     *
     * @return array{token: string, expires: int}|null
     */
    public static function getCsrfToken(): ?array
    {
        $data = self::get(CSRF_TOKEN_NAME);
        return is_array($data) ? $data : null;
    }

    // ── Internal ─────────────────────────────────────────────

    private static function ensureStarted(): void
    {
        if (!self::$started) {
            self::start();
        }
    }
}
