<?php

/**
 * ============================================================
 * core/EnvLoader.php
 * ============================================================
 * Simple .env file parser for loading environment variables.
 * 
 * Loads key=value pairs from a .env file into $_ENV and putenv().
 * Supports:
 *  - Comments (lines starting with #)
 *  - Quoted values (single and double quotes)
 *  - Empty values
 *  - Default values via get() method
 * ============================================================
 */

class EnvLoader
{
    private static bool $loaded = false;

    /**
     * Load environment variables from a .env file.
     *
     * @param string $path Path to the .env file
     * @return bool True if file was loaded successfully
     */
    public static function load(string $path): bool
    {
        if (self::$loaded) {
            return true;
        }

        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return false;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Parse key=value
            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove surrounding quotes
            if (preg_match('/^([\'"])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }

            // Set in environment
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }

        self::$loaded = true;
        return true;
    }

    /**
     * Get an environment variable with optional default.
     *
     * @param string $key     The environment variable name
     * @param mixed  $default Default value if not found
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Check if an environment variable exists.
     *
     * @param string $key The environment variable name
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_ENV[$key]) || getenv($key) !== false;
    }

    /**
     * Get a boolean environment variable.
     *
     * @param string $key     The environment variable name
     * @param bool   $default Default value if not found
     * @return bool
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key);

        if ($value === null) {
            return $default;
        }

        return in_array(strtolower((string) $value), ['true', '1', 'yes', 'on'], true);
    }

    /**
     * Get an integer environment variable.
     *
     * @param string $key     The environment variable name
     * @param int    $default Default value if not found
     * @return int
     */
    public static function getInt(string $key, int $default = 0): int
    {
        $value = self::get($key);
        return $value !== null ? (int) $value : $default;
    }
}
