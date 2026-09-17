# Gestión de Alumnos

Aplicación CRUD desarrollada con PHP, MySQL, HTML, CSS y JavaScript.

## Instalación en AlwaysData

1. Abre phpMyAdmin para la base `jongox_estudiante`.
2. Importa el archivo `jongox_estudiante.sql`.
3. Sube todo el contenido de esta carpeta al directorio web de tu sitio.
4. Asegúrate de usar PHP 8.1 o superior y tener habilitada la extensión `pdo_mysql`.
5. Abre el sitio. La conexión ya está definida en `config.php`.

## Funciones

- Registrar, editar y eliminar alumnos.
- Buscar por nombre, cédula o teléfono.
- Diseño adaptable a celular y escritorio.
- Consultas preparadas, escape HTML y protección CSRF.
