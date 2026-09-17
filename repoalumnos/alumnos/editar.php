<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: ../index.php'); exit; }
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM alumnos WHERE id = ?");
$stmt->execute([$id]);
$alumno = $stmt->fetch();
if (!$alumno) { header('Location: ../index.php'); exit; }
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar Alumno</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<header class="topbar"><h1>Editar Alumno</h1><a class="btn ghost" href="../index.php">← Volver</a></header>
<main class="container"><section class="card form-card">
<form action="actualizar.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= (int)$alumno['id'] ?>">
<label>Nombre completo *<input type="text" name="nombre" maxlength="150" value="<?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?>" required></label>
<label>Identificación *<input type="text" name="identificacion" maxlength="50" value="<?= htmlspecialchars($alumno['identificacion'], ENT_QUOTES, 'UTF-8') ?>" required></label>
<label>Teléfono *<input type="tel" name="telefono" maxlength="30" value="<?= htmlspecialchars($alumno['telefono'], ENT_QUOTES, 'UTF-8') ?>" required></label>
<p>Fotografía actual:</p>
<img class="preview" src="../uploads/<?= htmlspecialchars($alumno['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="Fotografía actual">
<label>Nueva fotografía <span class="hint">Opcional. JPG, JPEG, PNG o WEBP. Máximo 5 MB.</span>
<input type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label>
<button class="btn primary" type="submit">Actualizar alumno</button>
</form></section></main>
<script src="../assets/js/script.js"></script>
</body></html>
