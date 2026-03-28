<?php

/**
 * ============================================================
 * app/middleware/CsrfMiddleware.php
 * ============================================================
 * Validates CSRF tokens on all state-changing requests
 * (POST, PUT, PATCH, DELETE).
 *
 * Skip paths can be whitelisted (e.g. webhook endpoints).
 *
 * Usage — apply globally or per-route:
 *   $router->post('/path', 'Controller@method', middleware: ['CsrfMiddleware']);
 * ============================================================
 */

class CsrfMiddleware extends Middleware
{
    /** HTTP methods that must carry a valid CSRF token */
    private array $protectedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /** URI patterns exempt from CSRF checking (e.g. webhooks) */
    private array $skipPaths = [
        // '/webhooks/payment',
    ];

    /**
     * Handle the incoming request.
     *
     * @param  callable $next
     * @return mixed
     */
    public function handle(callable $next): mixed
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        if (!in_array($method, $this->protectedMethods, true)) {
            return $next();
        }

        // Check skip list
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        foreach ($this->skipPaths as $path) {
            if (str_starts_with($uri, $path)) {
                return $next();
            }
        }

        // Retrieve submitted token (from POST body or custom header for AJAX)
        $submittedToken = $_POST[CSRF_TOKEN_NAME]
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? '';

        if (!verifyCsrfToken($submittedToken)) {
            $this->abort(403, 'CSRF token invalid or expired. Please go back and try again.');
        }

        return $next();
    }
}
