# REPOALUMNOS

Aplicación PHP + MySQL para gestionar alumnos.

## Estructura

Todos los archivos de la aplicación están en la raíz. La única carpeta es:

.github/workflows/deployalways.yml

## AlwaysData

1. Sube el contenido del repositorio al directorio raíz configurado para tu sitio PHP.
2. Asegúrate de que `index.php` esté directamente en el directorio raíz del sitio.
3. En la misma carpeta de `index.php`, crea `local.php`.
4. Usa este contenido y reemplaza usuario y contraseña por los datos reales de tu base de datos:

```php
<?php
return [
    'DB_HOST' => 'mysql-misaelgaray.alwaysdata.net',
    'DB_NAME' => 'misaelgaray_repoalumnos',
    'DB_USER' => 'TU_USUARIO',
    'DB_PASS' => 'TU_CONTRASEÑA',
];
```

5. No subas `local.php` a GitHub.
6. Al abrir `index.php`, la aplicación crea automáticamente la tabla `alumnos` si no existe.

## GitHub Actions

El archivo `.github/workflows/deployalways.yml` usa FTP. En GitHub crea estos Secrets:

- FTP_SERVER
- FTP_USERNAME
- FTP_PASSWORD

Usa los datos FTP que aparecen en tu cuenta de AlwaysData.

## Requisitos

- PHP 8.x
- PDO MySQL
- MySQL/MariaDB
- Permiso de escritura para que PHP pueda guardar las fotografías en la carpeta raíz.

## Campos

- Nombre
- Identificación
- Teléfono
- Imagen obligatoria al crear
