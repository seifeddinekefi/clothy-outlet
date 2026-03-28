<?php

/**
 * ============================================================
 * core/View.php
 * ============================================================
 * View renderer — responsible ONLY for rendering PHP templates.
 *
 * Features:
 *  - Template resolution from /app/views/
 *  - Layout (wrapper) support with yield zones
 *  - Data binding via extract()
 *  - Flash message injection
 *  - Partial/include helper
 *  - Output buffering — no stray output
 * ============================================================
 */

class View
{
    /** Path to the views directory */
    private string $viewPath;

    /** Active layout file name (relative to views/) */
    private string $layout = 'layouts/main';

    /** Variables passed to the view */
    private array $data = [];

    /** Captured content sections (yield zones) */
    private array $sections = [];

    /** Section currently being captured */
    private ?string $activeSection = null;

    public function __construct()
    {
        $this->viewPath = VIEW_PATH . '/';
    }

    // ── Layout Control ───────────────────────────────────────

    /**
     * Set the layout wrapper for this view.
     * Pass null or '' to render without a layout.
     */
    public function setLayout(?string $layout): static
    {
        $this->layout = $layout ?? '';
        return $this;
    }

    // ── Data Binding ─────────────────────────────────────────

    /**
     * Assign a single variable to the view.
     */
    public function with(string $key, mixed $value): static
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Assign an array of variables to the view.
     *
     * @param  array<string, mixed> $data
     */
    public function withMany(array $data): static
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    // ── Rendering ────────────────────────────────────────────

    /**
     * Render a view file, optionally wrapped in a layout.
     *
     * @param  string               $view   Dot-notation path relative to views/ (e.g. 'home.index')
     * @param  array<string, mixed> $data   Additional data merged with $this->data
     * @return string
     */
    public function render(string $view, array $data = []): string
    {
        $this->data = array_merge($this->data, $data);

        // Inject flash messages automatically
        $this->data['_flash'] = Session::getFlash();

        $viewContent = $this->captureFile($this->resolvePath($view), $this->data);

        if ($this->layout !== '') {
            $this->data['_view_content'] = $viewContent;
            return $this->captureFile($this->resolvePath($this->layout), $this->data);
        }

        return $viewContent;
    }

    /**
     * Directly output a rendered view (convenience wrapper).
     */
    public function display(string $view, array $data = []): void
    {
        echo $this->render($view, $data);
    }

    // ── Section / Yield (layout zones) ──────────────────────

    /**
     * Begin capturing a named section.
     */
    public function startSection(string $name): void
    {
        $this->activeSection = $name;
        ob_start();
    }

    /**
     * End capturing the current section.
     */
    public function endSection(): void
    {
        if ($this->activeSection === null) {
            throw new \RuntimeException('View::endSection() called without startSection().');
        }
        $this->sections[$this->activeSection] = ob_get_clean();
        $this->activeSection = null;
    }

    /**
     * Output a captured section (used inside layout files).
     *
     * @param  string $name     Section key
     * @param  string $default  Fallback content
     */
    public function yield(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    // ── Partials ─────────────────────────────────────────────

    /**
     * Include and render a partial view inline.
     *
     * @param  string               $partial  Dot-notation path (e.g. 'partials.navbar')
     * @param  array<string, mixed> $data
     */
    public function partial(string $partial, array $data = []): string
    {
        $merged = array_merge($this->data, $data);
        return $this->captureFile($this->resolvePath($partial), $merged);
    }

    // ── Helpers ──────────────────────────────────────────────

    /**
     * Generate a URL relative to BASE_URL.
     */
    public function url(string $path = ''): string
    {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Asset URL (relative to public/assets/).
     */
    public function asset(string $path): string
    {
        return $this->url('assets/' . ltrim($path, '/'));
    }

    // ── Internal ─────────────────────────────────────────────

    /**
     * Resolve dot-notation view name to file path.
     */
    private function resolvePath(string $view): string
    {
        $file = $this->viewPath . str_replace('.', '/', $view) . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("View not found: [{$view}] at {$file}");
        }

        return $file;
    }

    /**
     * Render a PHP file with variable scope isolation.
     *
     * @param  string               $filePath
     * @param  array<string, mixed> $data
     * @return string
     */
    private function captureFile(string $filePath, array $data): string
    {
        // Make View instance available as $view inside templates
        $data['view'] = $this;

        extract($data, EXTR_SKIP);

        ob_start();
        require $filePath;
        return ob_get_clean();
    }
}
