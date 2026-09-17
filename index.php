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
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <i class="bi bi-mortarboard-fill fs-3"></i>
                <span>Academia Pro</span>
            </a>
            <div class="d-flex align-items-center text-white-50 small">
                <i class="bi bi-people me-1"></i> Sistema de gestión de alumnos
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Panel de Alumnos</h1>
                <p class="text-muted mb-0">Administra el registro de alumnos de forma sencilla y profesional</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2">
                    <i class="bi bi-person-badge me-1"></i> <?php echo $total; ?> alumno<?php echo $total !== 1 ? 's' : ''; ?>
                </span>
            </div>
        </div>

        <!-- Alertas -->
        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo e($tipo_mensaje); ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : ($tipo_mensaje === 'warning' ? 'exclamation-triangle' : 'x-circle'); ?> me-2"></i>
                <?php echo e($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Formulario Nuevo Alumno -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-person-plus text-primary me-2"></i>Nuevo Alumno
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php" novalidate>
                            <input type="hidden" name="accion" value="crear">

                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-medium">Nombre completo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                           placeholder="Ej: Juan Pérez" required maxlength="255"
                                           value="<?php echo e($_POST['nombre'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="identificacion" class="form-label fw-medium">Identificación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control" id="identificacion" name="identificacion"
                                           placeholder="Cédula, DNI, pasaporte..." required maxlength="50"
                                           value="<?php echo e($_POST['identificacion'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="telefono" class="form-label fw-medium">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" id="telefono" name="telefono"
                                           placeholder="Ej: 3001234567" maxlength="30"
                                           value="<?php echo e($_POST['telefono'] ?? ''); ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-person-check me-2"></i>Registrar Alumno
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Listado de Alumnos -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-people text-primary me-2"></i>Listado de Alumnos
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($alumnos)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-person-x display-4 d-block mb-3 opacity-50"></i>
                                <p class="mb-0">No hay alumnos registrados todavía.</p>
                                <small>Utiliza el formulario de la izquierda para crear el primero.</small>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4" style="width:70px">#</th>
                                            <th>Nombre</th>
                                            <th>Identificación</th>
                                            <th>Teléfono</th>
                                            <th class="text-nowrap">Fecha</th>
                                            <th class="text-end pe-4" style="width:120px">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($alumnos as $alumno): ?>
                                            <tr>
                                                <td class="ps-4 fw-medium text-muted">#<?php echo (int)$alumno['id']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="avatar-circle">
                                                            <?php echo strtoupper(mb_substr($alumno['nombre'], 0, 1)); ?>
                                                        </div>
                                                        <span class="fw-medium"><?php echo e($alumno['nombre']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-muted">
                                                    <code class="bg-light px-2 py-1 rounded"><?php echo e($alumno['identificacion']); ?></code>
                                                </td>
                                                <td class="text-muted">
                                                    <?php if (!empty($alumno['telefono'])): ?>
                                                        <i class="bi bi-telephone-fill text-success me-1"></i>
                                                        <?php echo e($alumno['telefono']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-nowrap small text-muted">
                                                    <?php
                                                    $fecha = new DateTime($alumno['fecha_creacion']);
                                                    echo $fecha->format('d/m/Y H:i');
                                                    ?>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="edit.php?id=<?php echo (int)$alumno['id']; ?>"
                                                           class="btn btn-outline-primary" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="index.php?eliminar=<?php echo (int)$alumno['id']; ?>"
                                                           class="btn btn-outline-danger"
                                                           title="Eliminar"
                                                           onclick="return confirm('¿Estás seguro de eliminar este alumno?');">
                                                            <i class="bi bi-trash"></i>
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

    <footer class="bg-white border-top mt-5 py-4">
        <div class="container text-center text-muted small">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Academia Pro · Sistema de gestión de alumnos</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
