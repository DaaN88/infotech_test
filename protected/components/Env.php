<?php

declare(strict_types=1);

/**
 * Простое чтение переменных из .env без перезапуска контейнера.
 * Приоритет: getenv() -> .env -> значение по умолчанию.
 */
class Env
{
    private static  $cache = [];
    private static ?int $mtime = null;

    public static function get(string $key, $default = null)
    {
        $val = getenv($key);
        if ($val !== false) {
            return $val;
        }

        $env = self::load();
        return array_key_exists($key, $env) ? $env[$key] : $default;
    }

    private static function load(): array
    {
        $path = dirname(__DIR__, 2) . '/.env';
        if (!is_file($path)) {
            return [];
        }

        $mtime = filemtime($path) ?: 0;
        if (self::$mtime === $mtime && self::$cache) {
            return self::$cache;
        }

        $content = file_get_contents($path);
        $parsed = [];

        foreach (preg_split('/\\r?\\n/', (string) $content) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (strpos($line, '=') === false) {
                continue;
            }
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \\\"'");
            $parsed[$k] = $v;
        }

        self::$cache = $parsed;
        self::$mtime = $mtime;

        return $parsed;
    }
}
