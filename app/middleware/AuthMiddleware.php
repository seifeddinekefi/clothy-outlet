<?php

/**
 * ============================================================
 * app/middleware/AuthMiddleware.php
 * ============================================================
 * Protects routes that require an authenticated (logged-in) user.
 *
 * If the user is not logged in, they are redirected to the
 * login page with a flash error message.
 *
 * Designed to be injected into route groups or individual routes:
 *   $router->group(['middleware' => 'AuthMiddleware'], fn() => ...);
 * ============================================================
 */

class AuthMiddleware extends Middleware
{
    /**
     * Handle the incoming request.
     *
     * @param  callable $next  The next handler in the pipeline
     * @return mixed
     */
    public function handle(callable $next): mixed
    {
        if (!Session::isLoggedIn()) {
            return $this->redirectWithFlash(url('login'), 'error', 'Please log in to continue.');
        }

        return $next();
    }
}
