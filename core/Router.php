<?php

/**
 * ============================================================
 * core/Router.php
 * ============================================================
 * HTTP Router with:
 *  - Clean URL dispatch
 *  - Route parameters  (/product/{id})
 *  - GET / POST / DELETE / PUT / PATCH support
 *  - Named routes (generate URLs by name)
 *  - Route groups with prefix + middleware
 *  - Per-route middleware (MiddlewarePipeline)
 *  - 404 / 405 fallback handlers
 * ============================================================
 */

class Router
{
    /**
     * Registered routes, keyed by HTTP method.
     *
     * @var array<string, array<int, array{
     *   pattern:    string,
     *   handler:    string,
     *   middleware: string[],
     *   name:       string|null
     * }>>
     */
    private array $routes = [];

    /**
     * Map of named-route => URL template (before parameter substitution).
     *
     * @var array<string, string>
     */
    private array $namedRoutes = [];

    // ── Group State ──────────────────────────────────────────

    /** @var string Current group prefix */
    private string $groupPrefix = '';

    /** @var string[] Middleware inherited from the active group */
    private array $groupMiddleware = [];

    // ── Registration ─────────────────────────────────────────

    /**
     * Register a GET route.
     */
    public function get(string $path, string $handler, ?string $name = null, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $name, $middleware);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, string $handler, ?string $name = null, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $name, $middleware);
    }

    /**
     * Register a PUT route.
     */
    public function put(string $path, string $handler, ?string $name = null, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $name, $middleware);
    }

    /**
     * Register a PATCH route.
     */
    public function patch(string $path, string $handler, ?string $name = null, array $middleware = []): void
    {
        $this->addRoute('PATCH', $path, $handler, $name, $middleware);
    }

    /**
     * Register a DELETE route.
     */
    public function delete(string $path, string $handler, ?string $name = null, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $name, $middleware);
    }

    /**
     * Register the same handler for all HTTP methods.
     */
    public function any(string $path, string $handler, ?string $name = null, array $middleware = []): void
    {
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
            $this->addRoute($method, $path, $handler, $name, $middleware);
        }
    }

    // ── Route Groups ─────────────────────────────────────────

    /**
     * Define a group of routes sharing a prefix and/or middleware.
     *
     * @param  array{prefix?: string, middleware?: string|string[]} $attributes
     * @param  callable $callback  Receives $this (the Router)
     *
     * Example:
     *   $router->group(['prefix' => 'admin', 'middleware' => 'AdminMiddleware'], function($r) {
     *       $r->get('/dashboard', 'Admin\DashboardController@index', 'admin.dashboard');
     *   });
     */
    public function group(array $attributes, callable $callback): void
    {
        // Save parent state
        $previousPrefix     = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        // Merge group attributes
        $this->groupPrefix .= '/' . trim($attributes['prefix'] ?? '', '/');
        $this->groupPrefix  = rtrim($this->groupPrefix, '/');

        $groupMw = $attributes['middleware'] ?? [];
        if (is_string($groupMw)) {
            $groupMw = [$groupMw];
        }
        $this->groupMiddleware = array_merge($this->groupMiddleware, $groupMw);

        // Execute the route definitions in the callback
        $callback($this);

        // Restore parent state (support nesting)
        $this->groupPrefix     = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    // ── Dispatch ─────────────────────────────────────────────

    /**
     * Match the current HTTP request and invoke the appropriate controller action.
     */
    public function dispatch(): void
    {
        $method = $this->resolveMethod();
        $uri    = $this->resolveUri();

        // 1. Try to match against registered routes for this method
        if (!empty($this->routes[$method])) {
            foreach ($this->routes[$method] as $route) {
                $params = $this->match($route['pattern'], $uri);
                if ($params !== false) {
                    $this->invoke($route['handler'], $params, $route['middleware']);
                    return;
                }
            }
        }

        // 2. Check if the URI exists under a different method → 405
        foreach ($this->routes as $registeredMethod => $methodRoutes) {
            if ($registeredMethod === $method) {
                continue;
            }
            foreach ($methodRoutes as $route) {
                if ($this->match($route['pattern'], $uri) !== false) {
                    $this->methodNotAllowed();
                    return;
                }
            }
        }

        // 3. Not found → 404
        $this->notFound();
    }

    // ── Named Route URL Generation ────────────────────────────

    /**
     * Generate a URL for a named route.
     *
     * @param  string               $name
     * @param  array<string, mixed> $params  Route parameters to substitute
     * @return string  Absolute URL
     */
    public function route(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \InvalidArgumentException("Named route [{$name}] is not defined.");
        }

        $path = $this->namedRoutes[$name];

        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', (string) $value, $path);
        }

        // If any placeholders remain, throw
        if (preg_match('/\{[^}]+\}/', $path)) {
            throw new \InvalidArgumentException("Missing parameters for route [{$name}].");
        }

        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }

    // ── Internal ─────────────────────────────────────────────

    /**
     * Store a route definition.
     */
    private function addRoute(
        string  $method,
        string  $path,
        string  $handler,
        ?string $name,
        array   $inlineMiddleware
    ): void {
        $fullPath = $this->groupPrefix . '/' . ltrim($path, '/');
        $fullPath = $fullPath === '/' ? '/' : rtrim($fullPath, '/');

        $middleware = array_merge($this->groupMiddleware, $inlineMiddleware);

        $this->routes[$method][] = [
            'pattern'    => $fullPath,
            'handler'    => $handler,
            'middleware' => $middleware,
            'name'       => $name,
        ];

        if ($name !== null) {
            $this->namedRoutes[$name] = $fullPath;
        }
    }

    /**
     * Attempt to match a URI against a route pattern.
     * Returns an associative array of captured params on success,
     * an empty array if matched with no params, or false on no match.
     *
     * @return array<string, string>|false
     */
    private function match(string $pattern, string $uri): array|false
    {
        // Convert {param} tokens to named capture groups
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '@^' . $regex . '$@';

        if (!preg_match($regex, $uri, $matches)) {
            return false;
        }

        // Return only named captures (strip integer-indexed matches)
        return array_filter(
            $matches,
            fn($key) => is_string($key),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Resolve the request URI (strips query string, normalises slashes).
     */
    private function resolveUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip the public sub-path if the app lives in a subdirectory
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = '/' . trim(rawurldecode($uri), '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    /**
     * Resolve HTTP method, honouring _method override (for HTML forms).
     */
    private function resolveMethod(): string
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        // Support _method override in POST forms (PUT / PATCH / DELETE)
        if ($method === 'POST' && !empty($_POST['_method'])) {
            $override = strtoupper($_POST['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                $method = $override;
            }
        }

        return $method;
    }

    /**
     * Instantiate the controller and call the action, wrapped in middleware.
     *
     * @param  string                $handler   'ControllerName@method'
     * @param  array<string, string> $params    Route parameters
     * @param  string[]              $middleware Middleware class names
     */
    private function invoke(string $handler, array $params, array $middleware): void
    {
        [$controllerClass, $method] = explode('@', $handler, 2);

        // Support dotted sub-namespaces: Admin\DashboardController
        $controllerClass = str_replace('\\', '/', $controllerClass);
        $resolvedClass   = basename($controllerClass);

        // Build the final callable
        $action = function () use ($resolvedClass, $method, $params, $controllerClass): void {
            // Attempt to load controller from sub-directory (e.g. Admin/)
            $subDir = dirname($controllerClass);
            if ($subDir && $subDir !== '.') {
                $file = APP_PATH . '/controllers/' . $subDir . '/' . $resolvedClass . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
            }

            if (!class_exists($resolvedClass)) {
                throw new \RuntimeException("Controller [{$resolvedClass}] not found.");
            }

            $controller = new $resolvedClass();

            if (!method_exists($controller, $method)) {
                throw new \RuntimeException(
                    "Method [{$method}] does not exist on controller [{$resolvedClass}]."
                );
            }

            $controller->$method(...array_values($params));
        };

        if (empty($middleware)) {
            $action();
            return;
        }

        // Run through the middleware pipeline
        (new MiddlewarePipeline())
            ->pipe($middleware)
            ->run($action);
    }

    // ── 404 / 405 Handlers ────────────────────────────────────

    private function notFound(): void
    {
        http_response_code(404);

        $errorView = VIEW_PATH . '/errors/404.php';
        if (file_exists($errorView)) {
            $view = new View();
            $view->setLayout(null)->display('errors.404');
        } else {
            echo '<h1>404 — Page Not Found</h1>';
            echo '<p>The page you are looking for does not exist.</p>';
        }
    }

    private function methodNotAllowed(): void
    {
        http_response_code(405);
        echo '<h1>405 — Method Not Allowed</h1>';
    }
}
