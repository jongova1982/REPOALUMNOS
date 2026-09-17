<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/init.php';

$error = null;
$alumnos = [];

try {
    $pdo = getPDO();
    initializeDatabase($pdo);
    $stmt = $pdo->query("SELECT * FROM alumnos ORDER BY id DESC");
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alumnos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header class="header">
        <div>
            <h1>Gestión de Alumnos</h1>
            <p>PHP + MySQL</p>
        </div>
        <a class="btn" href="crear.php">+ Nuevo alumno</a>
    </header>

    <?php if ($error): ?>
        <div class="alert error">
            <h2>No se pudo iniciar el sistema</h2>
            <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <p>En AlwaysData verifica que <strong>local.php</strong> exista en la misma carpeta que <strong>index.php</strong> y tenga las credenciales correctas de MySQL.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <?php if (!$alumnos): ?>
                <div class="empty">
                    <h2>No hay alumnos registrados</h2>
                    <p>Comienza agregando el primer alumno.</p>
                    <a class="btn" href="crear.php">Registrar alumno</a>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Identificación</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($alumnos as $alumno): ?>
                            <tr>
                                <td>
                                    <img class="avatar" src="<?= htmlspecialchars($alumno['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto">
                                </td>
                                <td><?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($alumno['identificacion'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($alumno['telefono'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="actions">
                                    <a class="btn small" href="editar.php?id=<?= (int)$alumno['id'] ?>">Editar</a>
                                    <form action="eliminar.php" method="post" onsubmit="return confirm('¿Eliminar este alumno?');">
                                        <input type="hidden" name="id" value="<?= (int)$alumno['id'] ?>">
                                        <button class="btn danger small" type="submit">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<script src="script.js"></script>
</body>
</html>
