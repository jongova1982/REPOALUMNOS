<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

function initializeDatabase(PDO $pdo): void {
    $sql = "CREATE TABLE IF NOT EXISTS alumnos (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL,
        identificacion VARCHAR(50) NOT NULL,
        telefono VARCHAR(30) NOT NULL,
        imagen VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_nombre (nombre),
        INDEX idx_identificacion (identificacion)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $pdo->exec($sql);
}
