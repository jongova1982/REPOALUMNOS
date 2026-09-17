<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';

$pdo = getPDO();
$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare(
        "SELECT id, nombre, identificacion, telefono, imagen, created_at
         FROM alumnos
         WHERE nombre LIKE :q OR identificacion LIKE :q
         ORDER BY id DESC"
    );
    $stmt->execute(['q' => '%' . $search . '%']);
} else {
    $stmt = $pdo->query(
        "SELECT id, nombre, identificacion, telefono, imagen, created_at
         FROM alumnos ORDER BY id DESC"
    );
}
$alumnos = $stmt->fetchAll();
$msg = $_GET['msg'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gestión de Alumnos</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
  <div>
    <h1>Gestión de Alumnos</h1>
    <p>Registro y administración de alumnos</p>
  </div>
  <a class="btn primary" href="alumnos/crear.php">+ Nuevo Alumno</a>
</header>

<main class="container">
<?php if ($msg): ?>
  <div class="alert success"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<section class="toolbar">
  <form method="get" class="search">
    <input type="search" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
           placeholder="Buscar por nombre o identificación...">
    <button class="btn" type="submit">Buscar</button>
    <?php if ($search !== ''): ?><a class="btn ghost" href="index.php">Limpiar</a><?php endif; ?>
  </form>
</section>

<section class="card table-wrap">
<table>
<thead><tr><th>Fotografía</th><th>Nombre</th><th>Identificación</th><th>Teléfono</th><th>Acciones</th></tr></thead>
<tbody>
<?php if (!$alumnos): ?>
<tr><td colspan="5" class="empty">No hay alumnos registrados.</td></tr>
<?php else: foreach ($alumnos as $alumno): ?>
<tr>
<td><img class="avatar" src="uploads/<?= htmlspecialchars($alumno['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto de <?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?>"></td>
<td><?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars($alumno['identificacion'], ENT_QUOTES, 'UTF-8') ?></td>
<td><?= htmlspecialchars($alumno['telefono'], ENT_QUOTES, 'UTF-8') ?></td>
<td class="actions">
  <a class="btn small" href="alumnos/editar.php?id=<?= (int)$alumno['id'] ?>">Editar</a>
  <form method="post" action="alumnos/eliminar.php" onsubmit="return confirm('¿Está seguro de que desea eliminar este alumno?');">
    <input type="hidden" name="id" value="<?= (int)$alumno['id'] ?>">
    <button class="btn small danger" type="submit">Eliminar</button>
  </form>
</td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</section>
</main>
<script src="assets/js/script.js"></script>
</body>
</html>
