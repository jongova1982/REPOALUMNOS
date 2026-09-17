# Academia Pro - Sistema de Gestión de Alumnos

Aplicativo web profesional desarrollado en **PHP + MySQL** para gestionar alumnos.

## Características

- ✅ Crear nuevos alumnos (nombre, identificación, teléfono)
- ✅ Listar todos los alumnos ordenados por fecha
- ✅ Editar alumnos existentes
- ✅ Eliminar alumnos
- ✅ Creación automática de la tabla `alumnos` si no existe
- ✅ Interfaz moderna y profesional
- ✅ Diseño responsive (móvil y escritorio)
- ✅ Validación de campos obligatorios
- ✅ Mensajes de confirmación y alertas

## Requisitos

- PHP 7.4 o superior (con extensión `mysqli`)
- Servidor web (Apache, Nginx, XAMPP, Laragon, etc.)
- Acceso a la base de datos MySQL configurada

## Credenciales de base de datos

Las credenciales ya están configuradas en `config.php`:

- **Host:** mysql-vinasco.alwaysdata.net
- **Usuario:** vinasco
- **Clave:** clase12345
- **Base de datos:** vinasco_repoenvios

## Instalación

1. Sube la carpeta `alumnos_app` a tu servidor web (o colócala en `htdocs` / `www`).
2. Asegúrate de que el servidor tenga acceso a internet (para conectar con la BD remota).
3. Abre en el navegador: `http://tu-servidor/alumnos_app/`

La tabla se crea automáticamente la primera vez que se accede a la aplicación.

## Estructura de archivos

```
alumnos_app/
├── config.php          # Conexión a BD + creación de tabla
├── index.php           # Listado + formulario de nuevo alumno
├── edit.php            # Edición de alumnos
├── assets/
│   └── style.css       # Estilos personalizados
└── README.md
```

## Tabla creada automáticamente

```sql
CREATE TABLE IF NOT EXISTS alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    identificacion VARCHAR(50) NOT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---
Desarrollado para uso educativo / demostración.
