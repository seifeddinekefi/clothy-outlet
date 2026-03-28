<?php

/**
 * ============================================================
 * core/Middleware.php
 * ============================================================
 * Base Middleware contract.
 *
 * All middleware classes in /app/middleware/ must extend this
 * class and implement handle().
 *
 * handle() receives a $next callable representing the next handler
 * in the pipeline. Middleware can:
 *  - Inspect / modify the request before calling $next()
 *  - Block the request entirely (redirect / abort)
 *  - Modify the response after calling $next()
 *
 * Usage in Router (automatic via route definition):
 *   $router->get('/path', 'Controller@method', name: 'name')
 *          ->middleware('AuthMiddleware');
 *
 * Or declared on a route group:
 *   $router->group(['middleware' => 'AuthMiddleware'], fn() => ...);
 * ============================================================
 */

abstract class Middleware
{
    /**
     * Handle the incoming request.
     *
     * @param  callable $next  The next handler in the pipeline.
     *                         Call $next() to pass control forward.
     * @return mixed
     */
    abstract public function handle(callable $next): mixed;

    // ── Shared Utilities available to all middleware ──────────

    /**
     * Terminate execution with a redirect.
     */
    protected function redirect(string $url, int $status = 302): never
    {
        http_response_code($status);
        header('Location: ' . $url);
        exit();
    }

    /**
     * Terminate with an HTTP error code.
     */
    protected function abort(int $code = 403, string $message = ''): never
    {
        http_response_code($code);
        echo $message ?: 'HTTP ' . $code;
        exit();
    }

    /**
     * Send a flash message + redirect in one call.
     */
    protected function redirectWithFlash(string $url, string $type, string $message): never
    {
        Session::flash($type, $message);
        $this->redirect($url);
    }
}


/**
 * ============================================================
 * MiddlewarePipeline
 * ============================================================
 * Executes an ordered list of middleware classes around a
 * core handler (the controller action).
 *
 * Based on the "pipeline / decorator" pattern used by
 * frameworks such as Laravel.
 * ============================================================
 */
class MiddlewarePipeline
{
    /** @var string[] Fully-qualified middleware class names */
    private array $middlewareStack = [];

    /**
     * Register middleware class names to run (in order).
     *
     * @param  string[] $stack
     */
    public function pipe(array $stack): static
    {
        $this->middlewareStack = array_merge($this->middlewareStack, $stack);
        return $this;
    }

    /**
     * Execute the pipeline, wrapping $destination at the centre.
     *
     * @param  callable $destination  The final handler (controller action)
     * @return mixed
     */
    public function run(callable $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewareStack),
            function (callable $next, string $middlewareClass): callable {
                return function () use ($next, $middlewareClass): mixed {
                    /** @var Middleware $instance */
                    $instance = new $middlewareClass();
                    return $instance->handle($next);
                };
            },
            $destination
        );

        return $pipeline();
    }
}
