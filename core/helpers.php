<?php

/**
 * ============================================================
 * core/helpers.php
 * ============================================================
 * Global utility functions.
 *
 * Sections:
 *  1. Security   — sanitize, escape, CSRF
 *  2. Output     — e(), dump(), dd()
 *  3. URL        — url(), asset(), route()
 *  4. Redirects  — redirect(), redirectBack()
 *  5. String     — str_limit(), slug(), uuid()
 *  6. Array      — array_get(), array_flatten()
 *  7. Date       — now(), timeAgo()
 *  8. Request    — request(), isPost(), isAjax()
 *  9. Response   — json_response(), abort()
 * 10. Debug      — dd(), dump()
 * ============================================================
 */

// ══════════════════════════════════════════════════════════════
// 1. SECURITY
// ══════════════════════════════════════════════════════════════

/**
 * Sanitise a value for safe use in application logic.
 * - Trims whitespace
 * - Strips HTML/PHP tags
 * - Converts special characters
 *
 * @param  mixed $value
 * @return mixed  String values are sanitised; arrays are recursively sanitised.
 */
function sanitize(mixed $value): mixed
{
    if (is_array($value)) {
        return array_map('sanitize', $value);
    }
    if (is_string($value)) {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    return $value;
}

/**
 * Escape a value for safe HTML output (XSS prevention).
 * Alias of htmlspecialchars with sane defaults.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Escape a value for use inside an HTML attribute.
 */
function attr(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Decode HTML special characters (for display inside <textarea> etc.).
 */
function html_decode(string $value): string
{
    return htmlspecialchars_decode($value, ENT_QUOTES);
}

// ── CSRF ──────────────────────────────────────────────────────

/**
 * Generate (or reuse existing) CSRF token.
 * Stores the token in the session with an expiry timestamp.
 *
 * @return string  The raw token value.
 */
function generateCsrfToken(): string
{
    $existing = Session::getCsrfToken();

    // Reuse a valid token (not expired)
    if ($existing && $existing['expires'] > time()) {
        return $existing['token'];
    }

    $token = bin2hex(random_bytes(32));
    Session::setCsrfToken($token);
    return $token;
}

/**
 * Verify a submitted CSRF token against the session.
 *
 * @param  string $submittedToken
 * @return bool
 */
function verifyCsrfToken(string $submittedToken): bool
{
    $stored = Session::getCsrfToken();

    if (!$stored) {
        return false;
    }

    if (time() > $stored['expires']) {
        Session::delete(CSRF_TOKEN_NAME);
        return false;
    }

    return hash_equals($stored['token'], $submittedToken);
}

/**
 * Render a hidden CSRF input field for use in HTML forms.
 *
 * @return string  Raw HTML (NOT escaped — intended for direct output in templates)
 */
function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . e($token) . '">';
}

/**
 * Render a hidden _method override field for HTML forms.
 * Allows PUT / PATCH / DELETE via HTML <form>.
 *
 * @param  string $method  'PUT' | 'PATCH' | 'DELETE'
 * @return string
 */
function methodField(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
}


// ══════════════════════════════════════════════════════════════
// 2. URL & ROUTING
// ══════════════════════════════════════════════════════════════

/**
 * Generate a URL relative to BASE_URL.
 *
 * @param  string $path  Relative path (will be URL-encoded if needed)
 * @return string
 */
function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Generate an asset URL relative to public/assets/.
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Format a price with the configured currency symbol.
 *
 * @param  float|int|string $amount
 * @param  int              $decimals
 * @return string           e.g. "99.00 TND"
 */
function formatPrice($amount, int $decimals = 2): string
{
    $formatted = number_format((float) $amount, $decimals);
    $symbol = defined('APP_CURRENCY_SYMBOL') ? APP_CURRENCY_SYMBOL : 'TND';
    $position = defined('APP_CURRENCY_POSITION') ? APP_CURRENCY_POSITION : 'after';

    if ($position === 'after') {
        return $formatted . ' ' . $symbol;
    }
    return $symbol . $formatted;
}

/**
 * Read a field from an array/object product record.
 */
function productField(array|object|null $product, string $field, mixed $default = null): mixed
{
    if (is_array($product)) {
        return $product[$field] ?? $default;
    }
    if (is_object($product)) {
        return $product->{$field} ?? $default;
    }
    return $default;
}

/**
 * Resolve badge metadata for a product card.
 * Sale badges always take priority over all other badge types.
 *
 * @return array{show: bool, label: string, class: string, type: string, sale_percent: int}
 */
function productBadgeMeta(array|object|null $product): array
{
    $price = (float) productField($product, 'price', 0);
    $compareRaw = productField($product, 'compare_price');
    $compare = is_numeric($compareRaw) ? (float) $compareRaw : 0;

    $salePercent = 0;
    if ($price > 0 && $compare > $price) {
        $salePercent = (int) round((1 - ($price / $compare)) * 100);
        $salePercent = max(1, $salePercent);
    }

    if ($salePercent > 0) {
        return [
            'show' => true,
            'label' => '-' . $salePercent . '%',
            'class' => 'badge-sale',
            'type' => 'sale',
            'sale_percent' => $salePercent,
        ];
    }

    $badgeType = strtolower(trim((string) productField($product, 'badge_type', 'auto')));
    if ($badgeType === '') {
        $badgeType = 'auto';
    }

    $badgeText = trim((string) productField($product, 'badge_text', ''));

    if ($badgeType === 'auto') {
        $badgeType = ((int) productField($product, 'is_featured', 0) === 1) ? 'new' : 'none';
    }

    if ($badgeText !== '') {
        $classMap = [
            'new' => 'badge-new',
            'hot' => 'badge-hot',
            'limited' => 'badge-limited',
            'bestseller' => 'badge-bestseller',
            'none' => 'badge-custom',
        ];

        return [
            'show' => true,
            'label' => $badgeText,
            'class' => $classMap[$badgeType] ?? 'badge-custom',
            'type' => 'custom',
            'sale_percent' => 0,
        ];
    }

    $badgeMap = [
        'none' => [false, '', ''],
        'new' => [true, 'New', 'badge-new'],
        'hot' => [true, 'Hot', 'badge-hot'],
        'limited' => [true, 'Limited', 'badge-limited'],
        'bestseller' => [true, 'Bestseller', 'badge-bestseller'],
    ];

    [$show, $label, $class] = $badgeMap[$badgeType] ?? [false, '', ''];

    return [
        'show' => $show,
        'label' => $label,
        'class' => $class,
        'type' => $badgeType,
        'sale_percent' => 0,
    ];
}

/**
 * Return a public URL for a product image stored in the DB (primary_image column).
 * Falls back to the placeholder if the file doesn't exist on disk yet.
 *
 * @param  string|null $dbPath  e.g. 'products/tsh-001-wht-1.jpg'
 * @return string
 */
function productImg(?string $dbPath): string
{
    static $placeholder = null;
    if ($placeholder === null) {
        $placeholder = asset('images/products/blazer.jpg');
    }
    if (!$dbPath) {
        return $placeholder;
    }

    // Check if this is an uploaded image (starts with 'uploads/')
    if (str_starts_with($dbPath, 'uploads/')) {
        $disk = BASE_PATH . '/public/' . ltrim($dbPath, '/');
        if (file_exists($disk)) {
            // Return URL to the uploads directory
            return rtrim(BASE_URL, '/') . '/' . ltrim($dbPath, '/');
        }
        return $placeholder;
    }

    // Legacy: check in assets/images directory
    $disk = BASE_PATH . '/public/assets/images/' . ltrim($dbPath, '/');
    return file_exists($disk)
        ? asset('images/' . ltrim($dbPath, '/'))
        : $placeholder;
}

/**
 * Generate a URL for a named route.
 *
 * @param  string               $name
 * @param  array<string, mixed> $params
 * @return string
 */
function route(string $name, array $params = []): string
{
    global $router;
    return $router->route($name, $params);
}


// ══════════════════════════════════════════════════════════════
// 3. REDIRECTS
// ══════════════════════════════════════════════════════════════

/**
 * Redirect to a URL and terminate.
 */
function redirect(string $url, int $status = 302): never
{
    http_response_code($status);
    header('Location: ' . $url);
    exit();
}

/**
 * Redirect back to the referring page, or a fallback URL.
 */
function redirectBack(string $fallback = '/'): never
{
    redirect($_SERVER['HTTP_REFERER'] ?? $fallback);
}


// ══════════════════════════════════════════════════════════════
// 4. STRING UTILITIES
// ══════════════════════════════════════════════════════════════

/**
 * Truncate a string to $limit characters, appending $append if truncated.
 */
function str_limit(string $value, int $limit = 100, string $append = '…'): string
{
    if (mb_strlen($value) <= $limit) {
        return $value;
    }
    return mb_substr($value, 0, $limit) . $append;
}

/**
 * Convert a string to a URL-friendly slug.
 *
 * @param  string $separator  Separator between words ('-' by default)
 * @return string
 */
function slug(string $value, string $separator = '-'): string
{
    // Transliterate non-ASCII characters
    $value = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $value)
        ?? strtolower($value);
    $value = preg_replace('/[^a-z0-9\s-_]/', '', $value);
    $value = preg_replace('/[\s\-_]+/', $separator, trim($value));
    return trim($value, $separator);
}

/**
 * Generate a UUID v4.
 */
function uuid(): string
{
    $data    = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0F) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3F) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Convert a string to PascalCase.
 */
function pascal_case(string $value): string
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
}

/**
 * Convert a string to camelCase.
 */
function camel_case(string $value): string
{
    return lcfirst(pascal_case($value));
}

/**
 * Mask a string (useful for partially hiding emails, card numbers, etc.).
 *
 * @param  string $value
 * @param  int    $showStart  Characters to reveal at the start
 * @param  int    $showEnd    Characters to reveal at the end
 * @param  string $mask       Masking character
 * @return string
 */
function mask(string $value, int $showStart = 2, int $showEnd = 2, string $mask = '*'): string
{
    $len = mb_strlen($value);
    if ($len <= ($showStart + $showEnd)) {
        return str_repeat($mask, $len);
    }
    $masked = $showStart > 0 ? mb_substr($value, 0, $showStart) : '';
    $masked .= str_repeat($mask, $len - $showStart - $showEnd);
    $masked .= $showEnd > 0 ? mb_substr($value, -$showEnd) : '';
    return $masked;
}


// ══════════════════════════════════════════════════════════════
// 5. ARRAY UTILITIES
// ══════════════════════════════════════════════════════════════

/**
 * Get a value from a nested array using dot notation.
 *
 * @param  array<string, mixed> $array
 * @param  string               $key    e.g. 'user.address.city'
 * @param  mixed                $default
 * @return mixed
 */
function array_get(array $array, string $key, mixed $default = null): mixed
{
    foreach (explode('.', $key) as $segment) {
        if (!is_array($array) || !array_key_exists($segment, $array)) {
            return $default;
        }
        $array = $array[$segment];
    }
    return $array;
}

/**
 * Flatten a multi-dimensional array one level deep.
 *
 * @param  array<mixed> $array
 * @return array<mixed>
 */
function array_flatten(array $array): array
{
    $result = [];
    foreach ($array as $item) {
        if (is_array($item)) {
            $result = array_merge($result, $item);
        } else {
            $result[] = $item;
        }
    }
    return $result;
}


// ══════════════════════════════════════════════════════════════
// 6. DATE & TIME
// ══════════════════════════════════════════════════════════════

/**
 * Return the current datetime string.
 */
function now(string $format = 'Y-m-d H:i:s'): string
{
    return date($format);
}

/**
 * Human-readable "time ago" — e.g. "3 hours ago".
 *
 * @param  string|int $timestamp  Unix timestamp or datetime string
 * @return string
 */
function timeAgo(string|int $timestamp): string
{
    $time = is_string($timestamp) ? strtotime($timestamp) : $timestamp;
    $diff = time() - $time;

    return match (true) {
        $diff < 60       => 'just now',
        $diff < 3600     => floor($diff / 60)   . ' minute(s) ago',
        $diff < 86400    => floor($diff / 3600)  . ' hour(s) ago',
        $diff < 604800   => floor($diff / 86400) . ' day(s) ago',
        $diff < 2592000  => floor($diff / 604800)  . ' week(s) ago',
        $diff < 31536000 => floor($diff / 2592000) . ' month(s) ago',
        default          => floor($diff / 31536000) . ' year(s) ago',
    };
}


// ══════════════════════════════════════════════════════════════
// 7. REQUEST HELPERS
// ══════════════════════════════════════════════════════════════

/**
 * Retrieve a value from the request (POST preferred, then GET).
 */
function request(string $key, mixed $default = null): mixed
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;
    return $value !== null ? sanitize($value) : $default;
}

/**
 * True if the current request method is POST.
 */
function isPost(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
}

/**
 * True if the request carries the XMLHttpRequest header.
 */
function isAjax(): bool
{
    return (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');
}

/**
 * Return the current request URI.
 */
function currentUri(): string
{
    return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}


// ══════════════════════════════════════════════════════════════
// 8. RESPONSE HELPERS
// ══════════════════════════════════════════════════════════════

/**
 * Send a JSON response and terminate.
 *
 * @param  mixed $data
 * @param  int   $status
 */
function json_response(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit();
}

/**
 * Abort with an HTTP status code and message.
 */
function abort(int $code = 404, string $message = ''): never
{
    http_response_code($code);
    $defaults = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
    ];
    echo $message ?: ($defaults[$code] ?? 'Error ' . $code);
    exit();
}


// ══════════════════════════════════════════════════════════════
// 9. FORMATTING
// ══════════════════════════════════════════════════════════════

/**
 * Format a number as currency.
 *
 * @param  float  $amount
 * @param  string $currency  Currency symbol prefix
 * @param  int    $decimals
 * @return string
 */
function currency(float $amount, string $currency = '$', int $decimals = 2): string
{
    return $currency . number_format($amount, $decimals);
}

/**
 * Format a file size in human-readable form.
 *
 * @param  int $bytes
 * @return string  e.g. "2.4 MB"
 */
function format_bytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes >= 1024 && $i < 4; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}


// ══════════════════════════════════════════════════════════════
// 10. DEBUG
// ══════════════════════════════════════════════════════════════

/**
 * Pretty-print variables without terminating.
 */
function dump(mixed ...$values): void
{
    if (!APP_DEBUG) {
        return;
    }
    foreach ($values as $value) {
        echo '<pre style="background:#1e1e1e;color:#d4d4d4;padding:12px;border-radius:4px;font-size:13px;overflow:auto">';
        echo e(print_r($value, true));
        echo '</pre>';
    }
}

/**
 * Pretty-print variables and terminate execution (die + dump).
 */
function dd(mixed ...$values): never
{
    dump(...$values);
    exit(1);
}
