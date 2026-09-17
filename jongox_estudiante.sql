-- Base de datos: jongox_estudiante
-- Ejecute este archivo desde phpMyAdmin en AlwaysData.
-- En AlwaysData la base normalmente ya existe, por eso solo se crea la tabla.

SET NAMES utf8mb4;
SET time_zone = '-05:00';

CREATE TABLE IF NOT EXISTS alumnos (
    cedula VARCHAR(20) NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    PRIMARY KEY (cedula),
    INDEX idx_alumnos_nombre (nombre)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
