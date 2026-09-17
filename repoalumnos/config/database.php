<?php
declare(strict_types=1);

function envValue(string $key, string $default = ''): string {
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }

    $envFile = __DIR__ . '/../.env';
    if (is_file($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines !== false) {
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                [$name, $val] = array_pad(explode('=', $line, 2), 2, '');
                $name = trim($name);
                $val = trim($val, " \t\n\r\0\x0B\"'");

                if ($name === $key && $val !== '') {
                    putenv("{$name}={$val}");
                    return $val;
                }
            }
        }
    }

    return $default;
}

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = envValue('DB_HOST', 'mysql-misaelgaray.alwaysdata.net');
    $db   = envValue('DB_NAME', 'misaelgaray_repoalumnos');
    $user = envValue('DB_USER', 'misaelgaray');
    $pass = envValue('DB_PASS', 'Mgm1927.');

    if ($user === '' || $pass === '') {
        throw new RuntimeException('Faltan las credenciales de base de datos. Configura DB_USER y DB_PASS en el entorno o en .env.');
    }

    $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
