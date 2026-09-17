<?php
require_once 'config.php';
$conn = getConnection();

// Mensajes de feedback
$mensaje = '';
$tipo_mensaje = '';

// Procesar eliminación
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM alumnos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensaje = 'Alumno eliminado correctamente.';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al eliminar el alumno.';
        $tipo_mensaje = 'danger';
    }
    $stmt->close();
}

// Procesar nuevo alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $nombre         = trim($_POST['nombre'] ?? '');
    $identificacion = trim($_POST['identificacion'] ?? '');
    $telefono       = trim($_POST['telefono'] ?? '');

    if ($nombre === '' || $identificacion === '') {
        $mensaje = 'El nombre y la identificación son obligatorios.';
        $tipo_mensaje = 'warning';
    } else {
        $stmt = $conn->prepare("INSERT INTO alumnos (nombre, identificacion, telefono) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $identificacion, $telefono);
        if ($stmt->execute()) {
            $mensaje = 'Alumno registrado correctamente.';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al registrar el alumno.';
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
}

// Obtener todos los alumnos
$result = $conn->query("SELECT * FROM alumnos ORDER BY fecha_creacion DESC");
$alumnos = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$total   = count($alumnos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alumnos | Academia Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-mortarboard-fill"></i>
                Academia Pro
            </a>
            <div class="d-none d-md-flex align-items-center text-white-50">
                <i class="bi bi-building me-2"></i>
                Sistema de Gestión Escolar
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <!-- Header -->
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="mb-1">
                    <i class="bi bi-people-fill me-2" style="color:#0d9488"></i>
                    Panel de Alumnos
                </h1>
                <p>Registra, consulta y administra la información de los estudiantes</p>
            </div>
            <span class="stat-badge">
                <i class="bi bi-person-badge me-1"></i>
                <?php echo $total; ?> alumno<?php echo $total !== 1 ? 's' : ''; ?> registrado<?php echo $total !== 1 ? 's' : ''; ?>
            </span>
        </div>

        <!-- Alertas -->
        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo e($tipo_mensaje); ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?php echo $tipo_mensaje === 'success' ? 'check-circle-fill' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle-fill' : 'x-circle-fill'); ?> me-2"></i>
                <?php echo e($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Formulario Nuevo Alumno -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="school-accent"></div>
                    <div class="card-header">
                        <h5>
                            <i class="bi bi-person-plus-fill me-2"></i>
                            Registrar nuevo alumno
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php" novalidate>
                            <input type="hidden" name="accion" value="crear">

                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    Nombre completo <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                           placeholder="Ej: María González López" required maxlength="255"
                                           value="<?php echo e($_POST['nombre'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="identificacion" class="form-label">
                                    Identificación <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control" id="identificacion" name="identificacion"
                                           placeholder="Cédula, DNI o pasaporte" required maxlength="50"
                                           value="<?php echo e($_POST['identificacion'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="telefono" class="form-label">Teléfono de contacto</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" id="telefono" name="telefono"
                                           placeholder="Ej: 300 123 4567" maxlength="30"
                                           value="<?php echo e($_POST['telefono'] ?? ''); ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-person-check-fill me-2"></i>
                                Registrar alumno
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Listado de Alumnos -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="school-accent"></div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>
                            <i class="bi bi-journal-bookmark-fill me-2"></i>
                            Listado de estudiantes
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($alumnos)): ?>
                            <div class="empty-state">
                                <i class="bi bi-mortarboard"></i>
                                <p class="mb-1 fw-medium">Aún no hay alumnos registrados</p>
                                <small class="text-muted">Utiliza el formulario de la izquierda para agregar el primero</small>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4" style="width:65px">#</th>
                                            <th>Estudiante</th>
                                            <th>Identificación</th>
                                            <th>Teléfono</th>
                                            <th class="text-nowrap">Registro</th>
                                            <th class="text-end pe-4" style="width:110px">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($alumnos as $alumno): ?>
                                            <tr>
                                                <td class="ps-4 text-muted fw-medium">
                                                    #<?php echo (int)$alumno['id']; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="avatar-circle">
                                                            <?php echo strtoupper(mb_substr($alumno['nombre'], 0, 1)); ?>
                                                        </div>
                                                        <span class="fw-semibold"><?php echo e($alumno['nombre']); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="id-badge"><?php echo e($alumno['identificacion']); ?></span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($alumno['telefono'])): ?>
                                                        <span class="text-success">
                                                            <i class="bi bi-telephone-fill me-1"></i>
                                                            <?php echo e($alumno['telefono']); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-nowrap small text-muted">
                                                    <?php
                                                    $fecha = new DateTime($alumno['fecha_creacion']);
                                                    echo $fecha->format('d/m/Y');
                                                    ?>
                                                    <br>
                                                    <span class="opacity-75"><?php echo $fecha->format('H:i'); ?></span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="edit.php?id=<?php echo (int)$alumno['id']; ?>"
                                                           class="btn btn-outline-primary" title="Editar alumno">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        <a href="index.php?eliminar=<?php echo (int)$alumno['id']; ?>"
                                                           class="btn btn-outline-danger"
                                                           title="Eliminar alumno"
                                                           onclick="return confirm('¿Estás seguro de eliminar a este alumno?');">
                                                            <i class="bi bi-trash3"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container text-center">
            <p>
                <i class="bi bi-mortarboard me-1"></i>
                &copy; <?php echo date('Y'); ?> Academia Pro · Sistema de Gestión de Alumnos
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
