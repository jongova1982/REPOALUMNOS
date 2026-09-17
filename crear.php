<?php
$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo alumno</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container narrow">
    <div class="card">
        <h1>Registrar alumno</h1>
        <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <form action="guardar.php" method="post" enctype="multipart/form-data">
            <label>Nombre completo
                <input type="text" name="nombre" maxlength="150" required>
            </label>
            <label>Identificación
                <input type="text" name="identificacion" maxlength="50" required>
            </label>
            <label>Teléfono
                <input type="text" name="telefono" maxlength="30" required>
            </label>
            <label>Imagen
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" required>
            </label>
            <div class="actions">
                <a class="btn secondary" href="index.php">Cancelar</a>
                <button class="btn" type="submit">Guardar alumno</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
