<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nuevo Alumno</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<header class="topbar"><h1>Nuevo Alumno</h1><a class="btn ghost" href="../index.php">← Volver</a></header>
<main class="container"><section class="card form-card">
<form action="guardar.php" method="post" enctype="multipart/form-data" id="alumnoForm">
<label>Nombre completo *
<input type="text" name="nombre" maxlength="150" required></label>
<label>Identificación *
<input type="text" name="identificacion" maxlength="50" required></label>
<label>Teléfono *
<input type="tel" name="telefono" maxlength="30" required></label>
<label>Fotografía * <span class="hint">JPG, JPEG, PNG o WEBP. Máximo 5 MB.</span>
<input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required></label>
<img id="preview" class="preview hidden" alt="Vista previa">
<button class="btn primary" type="submit">Guardar alumno</button>
</form></section></main>
<script src="../assets/js/script.js"></script>
</body></html>
