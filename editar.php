<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/init.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $pdo = getPDO();
    initializeDatabase($pdo);
    $stmt = $pdo->prepare('SELECT * FROM alumnos WHERE id = ?');
    $stmt->execute([$id]);
    $alumno = $stmt->fetch();

    if (!$alumno) {
        header('Location: index.php');
        exit;
    }
} catch (Throwable $e) {
    die('Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar alumno</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container narrow">
    <div class="card">
        <h1>Editar alumno</h1>
        <form action="actualizar.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= (int)$alumno['id'] ?>">
            <label>Nombre completo
                <input type="text" name="nombre" maxlength="150" required value="<?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?>">
            </label>
            <label>Identificación
                <input type="text" name="identificacion" maxlength="50" required value="<?= htmlspecialchars($alumno['identificacion'], ENT_QUOTES, 'UTF-8') ?>">
            </label>
            <label>Teléfono
                <input type="text" name="telefono" maxlength="30" required value="<?= htmlspecialchars($alumno['telefono'], ENT_QUOTES, 'UTF-8') ?>">
            </label>
            <label>Nueva imagen (opcional)
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
            </label>
            <img class="preview" src="<?= htmlspecialchars($alumno['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto actual">
            <div class="actions">
                <a class="btn secondary" href="index.php">Cancelar</a>
                <button class="btn" type="submit">Actualizar</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
