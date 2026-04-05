<?php

/**
 * ============================================================
 * app/middleware/GuestCheckoutMiddleware.php
 * ============================================================
 * Allows both authenticated users AND guests to access checkout.
 *
 * For guests:
 *  - Generates a guest_token stored in session
 *  - Allows the checkout flow to proceed without login
 *
 * For authenticated users:
 *  - Passes through normally
 *
 * Used on checkout routes instead of AuthMiddleware.
 * ============================================================
 */

class GuestCheckoutMiddleware extends Middleware
{
    /**
     * Handle the incoming request.
     *
     * @param  callable $next  The next handler in the pipeline
     * @return mixed
     */
    public function handle(callable $next): mixed
    {
        // If user is logged in, proceed normally
        if (Session::isLoggedIn()) {
            return $next();
        }

        // For guests, ensure we have a guest token in session
        if (!Session::has('guest_token')) {
            Session::set('guest_token', bin2hex(random_bytes(32)));
        }

        // Mark this as a guest checkout session
        Session::set('is_guest_checkout', true);

        return $next();
    }

    /**
     * Check if current session is a guest checkout.
     */
    public static function isGuestCheckout(): bool
    {
        return !Session::isLoggedIn() && Session::get('is_guest_checkout', false);
    }

    /**
     * Get the guest token for the current session.
     */
    public static function getGuestToken(): ?string
    {
        return Session::get('guest_token');
    }

    /**
     * Clear guest checkout session data (after order or registration).
     */
    public static function clearGuestSession(): void
    {
        Session::delete('guest_token');
        Session::delete('is_guest_checkout');
    }
}
