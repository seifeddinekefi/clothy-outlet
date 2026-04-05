<?php

/**
 * ============================================================
 * config/config.php
 * ============================================================
 * Central application configuration.
 * All environment-sensitive values are loaded from .env file.
 * Copy .env.example to .env and configure for your environment.
 * ============================================================
 */

// ─── Paths (needed before EnvLoader) ────────────────────────
define('BASE_PATH',   dirname(__DIR__));         // project root
define('CORE_PATH',   BASE_PATH . '/core');

// ─── Load Environment Variables ─────────────────────────────
require_once CORE_PATH . '/EnvLoader.php';
EnvLoader::load(BASE_PATH . '/.env');

// ─── Environment ────────────────────────────────────────────
define('APP_ENV',     EnvLoader::get('APP_ENV', 'development'));
define('APP_DEBUG',   EnvLoader::getBool('APP_DEBUG', APP_ENV === 'development'));

// ─── Application ────────────────────────────────────────────
define('APP_NAME',    'Clothy Outlet');
define('APP_VERSION', '1.0.0');
define('APP_LOCALE',  'en');
define('APP_CHARSET', 'UTF-8');

// ─── URL & Paths ────────────────────────────────────────────
define('BASE_URL',    EnvLoader::get('APP_URL', 'http://localhost/clothy/public'));

define('APP_PATH',    BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('VIEW_PATH',   APP_PATH  . '/views');
define('UPLOAD_PATH', BASE_PATH . '/uploads');

// ─── Database (credentials from .env) ────────────────────────
define('DB_HOST',     EnvLoader::get('DB_HOST', 'localhost'));
define('DB_PORT',     EnvLoader::get('DB_PORT', '3306'));
define('DB_NAME',     EnvLoader::get('DB_NAME', 'clothy_outlet'));
define('DB_USER',     EnvLoader::get('DB_USER', 'root'));
define('DB_PASS',     EnvLoader::get('DB_PASS', ''));
define('DB_CHARSET',  'utf8mb4');

// ─── Session ─────────────────────────────────────────────────
define('SESSION_NAME',     'CLOTHY_SESS');
define('SESSION_LIFETIME', 7200);          // seconds (2 hours)
define('SESSION_SECURE',   EnvLoader::getBool('SESSION_SECURE', false));
define('SESSION_HTTPONLY',  true);
define('SESSION_SAMESITE',  'Strict');

// ─── Security ────────────────────────────────────────────────
define('CSRF_TOKEN_NAME', '_csrf_token');
define('CSRF_TOKEN_TTL',  3600);           // 1 hour

// ─── Pagination ──────────────────────────────────────────────
define('PER_PAGE', 12);

// ─── Currency & Shipping ─────────────────────────────────────
define('APP_CURRENCY_SYMBOL', 'TND');
define('APP_CURRENCY_POSITION', 'after');  // 'before' for "TND 99.00" or 'after' for "99.00 TND"
define('SHIPPING_FEE', 8.00);

// ─── Uploads ─────────────────────────────────────────────────
define('UPLOAD_MAX_SIZE',  5 * 1024 * 1024);  // 5 MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// ─── Error Reporting ─────────────────────────────────────────
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', BASE_PATH . '/storage/logs/error.log');
}

// ─── Default Timezone ────────────────────────────────────────
date_default_timezone_set('UTC');

// ─── Auto-loader ─────────────────────────────────────────────
// PSR-4 style class auto-loader covering /core and /app subdirs.
spl_autoload_register(function (string $class): void {
    $directories = [
        CORE_PATH . '/',
        APP_PATH  . '/controllers/',
        APP_PATH  . '/controllers/Admin/',
        APP_PATH  . '/models/',
        APP_PATH  . '/middleware/',
    ];

    foreach ($directories as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
