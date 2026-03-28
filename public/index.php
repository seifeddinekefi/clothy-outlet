<?php

/**
 * ============================================================
 * public/index.php
 * ============================================================
 * Application Entry Point — the ONLY publicly accessible PHP file.
 *
 * All HTTP requests are funnelled here via the .htaccess rewrite rule.
 *
 * Bootstrap order:
 *  1. Load configuration constants
 *  2. Load helper functions
 *  3. Auto-load core classes (via spl_autoload in config.php)
 *  4. Start session
 *  5. Load routes
 *  6. Dispatch request through the router
 * ============================================================
 */

declare(strict_types=1);

// ─── 1. Configuration ────────────────────────────────────────
require_once dirname(__DIR__) . '/config/config.php';

// ─── 2. Helpers ──────────────────────────────────────────────
require_once CORE_PATH . '/helpers.php';

// ─── 3. Core Classes (auto-loaded by spl_autoload in config.php)
// Explicit requires as a safety net:
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Session.php';
require_once CORE_PATH . '/Model.php';
require_once CORE_PATH . '/View.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Middleware.php';
require_once CORE_PATH . '/Router.php';

// ─── 4. Session ──────────────────────────────────────────────
Session::start();

// ─── 5. Router ───────────────────────────────────────────────
$router = new Router();
require_once CONFIG_PATH . '/routes.php';

// ─── 6. Dispatch ─────────────────────────────────────────────
$router->dispatch();
