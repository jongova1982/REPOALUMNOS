<?php
/**
 * Configuración de base de datos y creación automática de tablas
 * Gestión de Alumnos - Aplicativo profesional
 */

define('DB_HOST', 'mysql-vinasco.alwaysdata.net');
define('DB_USER', 'vinasco');
define('DB_PASS', 'clase1234');
define('DB_NAME', 'vinasco_repoenvios');

/**
 * Obtiene una conexión mysqli y asegura que la tabla exista
 */
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die('<div style="font-family:sans-serif;padding:40px;text-align:center;color:#c00;">
                <h2>Error de conexión</h2>
                <p>No se pudo conectar a la base de datos: ' . htmlspecialchars($conn->connect_error) . '</p>
             </div>');
    }

    $conn->set_charset('utf8mb4');

    // Crear tabla si no existe
    $sql = "CREATE TABLE IF NOT EXISTS alumnos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        identificacion VARCHAR(50) NOT NULL,
        telefono VARCHAR(30) DEFAULT NULL,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_identificacion (identificacion),
        INDEX idx_nombre (nombre)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if (!$conn->query($sql)) {
        die('Error al crear la tabla: ' . $conn->error);
    }

    return $conn;
}

/**
 * Escapa salida HTML de forma segura
 */
function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}
?>
