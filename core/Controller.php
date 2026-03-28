<?php

/**
 * ============================================================
 * core/Controller.php
 * ============================================================
 * Base Controller — abstract parent for all application controllers.
 *
 * Responsibilities:
 *  - Provide view rendering shortcut
 *  - Provide redirect / JSON response helpers
 *  - Input sanitisation access
 *  - No SQL, no direct HTML output beyond view delegation
 * ============================================================
 */

abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    // ── View Rendering ────────────────────────────────────────

    /**
     * Render a view and send it to the browser.
     *
     * @param  string               $view   Dot-notation (e.g. 'home.index')
     * @param  array<string, mixed> $data   Variables passed to the view
     * @param  int                  $status HTTP status code
     */
    protected function render(string $view, array $data = [], int $status = 200): void
    {
        http_response_code($status);
        $this->view->display($view, $data);
    }

    /**
     * Render a view without any layout wrapper.
     */
    protected function renderPartial(string $view, array $data = []): void
    {
        $this->view->setLayout(null);
        $this->view->display($view, $data);
    }

    // ── Redirect Helpers ─────────────────────────────────────

    /**
     * Redirect to a URL and terminate execution.
     *
     * @param  string $url
     * @param  int    $status  301 | 302 | 303 | 307 | 308
     */
    protected function redirect(string $url, int $status = 302): never
    {
        http_response_code($status);
        header('Location: ' . $url);
        exit();
    }

    /**
     * Redirect back to the referrer (or a fallback URL).
     */
    protected function redirectBack(string $fallback = '/'): never
    {
        $referrer = $_SERVER['HTTP_REFERER'] ?? $fallback;
        $this->redirect($referrer);
    }

    /**
     * Redirect to a named route (resolved via helper).
     *
     * @param  string               $name
     * @param  array<string, mixed> $params  Route parameters to substitute
     */
    protected function redirectRoute(string $name, array $params = []): never
    {
        $this->redirect(route($name, $params));
    }

    // ── JSON Responses ────────────────────────────────────────

    /**
     * Send a JSON response and terminate.
     *
     * @param  mixed $data
     * @param  int   $status  HTTP status code
     */
    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit();
    }

    /**
     * Send a standard JSON success envelope.
     *
     * @param  mixed  $data
     * @param  string $message
     */
    protected function jsonSuccess(mixed $data = null, string $message = 'OK'): never
    {
        $this->json(['success' => true, 'message' => $message, 'data' => $data]);
    }

    /**
     * Send a standard JSON error envelope.
     */
    protected function jsonError(string $message = 'Error', int $status = 400): never
    {
        $this->json(['success' => false, 'message' => $message], $status);
    }

    // ── Input Helpers ─────────────────────────────────────────

    /**
     * Retrieve and sanitise a GET parameter.
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? sanitize($_GET[$key]) : $default;
    }

    /**
     * Retrieve and sanitise a POST parameter.
     */
    protected function post(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? sanitize($_POST[$key]) : $default;
    }

    /**
     * Retrieve all sanitised POST data.
     *
     * @return array<string, mixed>
     */
    protected function postAll(): array
    {
        return array_map('sanitize', $_POST);
    }

    /**
     * Return true if the current request is POST.
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Return true if the request sends the X-Requested-With: XMLHttpRequest header.
     */
    protected function isAjax(): bool
    {
        return (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');
    }

    // ── CSRF ──────────────────────────────────────────────────

    /**
     * Validate the CSRF token submitted with a form.
     * Terminates with 403 on failure.
     */
    protected function verifyCsrf(): void
    {
        if (!verifyCsrfToken($this->post(CSRF_TOKEN_NAME, ''))) {
            http_response_code(403);
            exit('CSRF token mismatch.');
        }
    }

    // ── Flash Messages ────────────────────────────────────────

    /**
     * Set a flash message.
     */
    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    // ── Authentication State ──────────────────────────────────

    /**
     * Require the user to be logged in; redirect to login otherwise.
     */
    protected function requireAuth(string $loginRoute = '/login'): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Please log in to continue.');
            $this->redirect($loginRoute);
        }
    }

    /**
     * Require a specific role; redirect back with error if not authorised.
     */
    protected function requireRole(string $role, string $redirect = '/'): void
    {
        if (!Session::hasRole($role)) {
            Session::flash('error', 'Access denied.');
            $this->redirect($redirect);
        }
    }

    /**
     * Return the authenticated user array (or null).
     *
     * @return array<string, mixed>|null
     */
    protected function authUser(): ?array
    {
        return Session::user();
    }

    // ── 404 / Error Responses ─────────────────────────────────

    /**
     * Render a 404 page and terminate.
     */
    protected function notFound(): never
    {
        http_response_code(404);
        // Views can override this by creating errors/404.php
        if (file_exists(VIEW_PATH . '/errors/404.php')) {
            $this->view->setLayout(null)->display('errors.404');
        } else {
            echo '<h1>404 — Page Not Found</h1>';
        }
        exit();
    }
}
