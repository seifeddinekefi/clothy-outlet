<?php

/**
 * ============================================================
 * app/middleware/AdminMiddleware.php
 * ============================================================
 * Protects all /admin routes.
 *
 * Checks:
 *  1. User is authenticated (logged in)
 *  2. User carries the 'admin' role
 *
 * On failure, redirects to the admin login page.
 *
 * Admin login is intentionally excluded from this middleware —
 * its route is registered INSIDE the group but the middleware
 * handles the /admin/login path by allowing unauthenticated access
 * by checking the current URI.
 * ============================================================
 */

class AdminMiddleware extends Middleware
{
    /** Routes inside the /admin group that are publicly accessible */
    private array $publicPaths = [
        '/admin/login',
    ];

    /**
     * Handle the incoming request.
     *
     * @param  callable $next
     * @return mixed
     */
    public function handle(callable $next): mixed
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip sub-directory prefix if the app runs in a sub-folder
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // Allow public admin paths (e.g. login page) without auth
        foreach ($this->publicPaths as $path) {
            if (rtrim($uri, '/') === rtrim($path, '/')) {
                return $next();
            }
        }

        // Not logged in → redirect to admin login
        if (!Session::isLoggedIn()) {
            return $this->redirectWithFlash(url('admin/login'), 'error', 'Admin login required.');
        }

        // Logged in but not an admin → redirect to home
        if (!Session::hasRole('admin')) {
            return $this->redirectWithFlash(url(''), 'error', 'Access denied.');
        }

        return $next();
    }
}
