# Sistema de Gestión de Alumnos

Aplicación CRUD en PHP + MySQL para registrar alumnos con fotografía. Diseñada para trabajar localmente con XAMPP y desplegarse en AlwaysData, manteniendo las credenciales fuera de GitHub.

## Requisitos
- PHP 8.0 o superior.
- PDO MySQL habilitado.
- MySQL/MariaDB.
- Apache u otro servidor PHP.

## Configuración local
1. Copia `.env.example` como `.env`.
2. Completa `DB_USER` y `DB_PASS`.
3. Configura el mecanismo de variables de entorno de tu servidor. No subas `.env`.
4. Coloca el proyecto dentro de `htdocs` si utilizas XAMPP.
5. Crea/selecciona la base de datos `misaelgaray_repoalumnos`.
6. Asegúrate de que PHP tenga habilitada la extensión PDO MySQL.
7. Abre `index.php`.

> Nota: PHP no carga automáticamente un archivo `.env` sin una librería. En AlwaysData configura las variables de entorno del sitio o adapta `config/database.php` al mecanismo de configuración que tengas habilitado. No coloques la contraseña en GitHub.

## AlwaysData
Configura en el entorno del sitio:
- `DB_HOST=mysql-misaelgaray.alwaysdata.net`
- `DB_NAME=misaelgaray_repoalumnos`
- `DB_USER=TU_USUARIO`
- `DB_PASS=TU_CONTRASEÑA`

Sube el contenido del proyecto al directorio configurado para el sitio PHP. Verifica que `/uploads/` exista y tenga permisos de escritura para el usuario del servidor.

La tabla `alumnos` se crea automáticamente al registrar el primer alumno.

## GitHub
Antes de subir:
```bash
git init
git add .
git commit -m "Primer commit"
git branch -M main
git remote add origin URL_DEL_REPOSITORIO
git push -u origin main
```

Comprueba que `.env` y las fotografías reales no estén incluidos. `.gitignore` protege esos archivos.

## Estructura
- `index.php`: listado y búsqueda.
- `config/database.php`: conexión PDO.
- `database/init.php`: creación automática de tabla.
- `alumnos/`: operaciones CRUD.
- `assets/`: CSS y JavaScript.
- `uploads/`: fotografías subidas en producción.
- `.env.example`: plantilla sin secretos.

## Seguridad
Las fotografías se validan por MIME, tamaño y contenido de imagen. Se utilizan consultas preparadas y escape HTML. Las credenciales reales y datos personales no deben publicarse en GitHub.
