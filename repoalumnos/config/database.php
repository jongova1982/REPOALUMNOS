<?php
declare(strict_types=1);

function envValue(string $key, string $default = ''): string {
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = envValue('DB_HOST', 'mysql-misaelgaray.alwaysdata.net');
    $db   = envValue('DB_NAME', 'misaelgaray_repoalumnos');
    $user = envValue('DB_USER');
    $pass = envValue('DB_PASS');

    if ($user === '') {
        throw new RuntimeException('Faltan las credenciales de base de datos. Configure DB_USER y DB_PASS.');
    }

    $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
