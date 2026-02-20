<?php

// Конфигурация подключения к БД:
// 1) берем переменные из окружения;
// 2) если нет — читаем .env без необходимости перезапуска контейнера;
// 3) если нет и там — используем дефолты из docker-compose.
$env = static function (string $key, string $default) {
    static $dotEnv = null;

    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return $value;
    }

    // Лениво читаем .env из корня проекта при первом обращении (учитывает правки без рестарта контейнера)
    if ($dotEnv === null) {
        $dotEnv = [];

        $envPath = dirname(__DIR__, 2) . '/.env';

        if (is_readable($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (strpos(ltrim($line), '#') === 0) {
                    continue;
                }

                if (! str_contains($line, '=')) {
                    continue;
                }

                [$k, $v] = explode('=', $line, 2);

                $k = trim($k);
                $v = trim($v);

                $dotEnv[$k] = $v;
            }
        }
    }

    if (isset($dotEnv[$key]) && $dotEnv[$key] !== '') {
        return $dotEnv[$key];
    }

    return $default;
};

$host = $env('DB_HOST', 'db');
$port = $env('DB_PORT', '3306');
$name = $env('DB_NAME', 'infotek');
$user = $env('DB_USER', 'infotek');
$pass = $env('DB_PASSWORD', 'infotek');

return [
    'connectionString' => "mysql:host={$host};port={$port};dbname={$name}",
    'emulatePrepare' => true,
    'username' => $user,
    'password' => $pass,
    'charset' => 'utf8mb4',
];
