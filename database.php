<?php
declare(strict_types=1);

function getConfigValue(string $key): ?string
{
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }

    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }

    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }

    $localFile = __DIR__ . '/local.php';
    if (is_file($localFile)) {
        $local = require $localFile;
        if (is_array($local) && isset($local[$key]) && $local[$key] !== '') {
            return (string) $local[$key];
        }
    }

    return null;
}

function getPDO(): PDO
{
    $host = getConfigValue('DB_HOST') ?: getConfigValue('DATABASE_HOST');
    $name = getConfigValue('DB_NAME') ?: getConfigValue('DATABASE_NAME');
    $user = getConfigValue('DB_USER') ?: getConfigValue('DATABASE_USERNAME');
    $pass = getConfigValue('DB_PASS') ?? getConfigValue('DATABASE_PASSWORD');

    if (!$host || !$name || !$user || $pass === null) {
        throw new RuntimeException(
            'Faltan las credenciales de MySQL. Crea local.php junto a index.php en AlwaysData o configura DB_HOST, DB_NAME, DB_USER y DB_PASS.'
        );
    }

    $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
