<?php
require_once 'config.php';
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Obtener el alumno
$stmt = $conn->prepare("SELECT * FROM alumnos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$alumno = $result->fetch_assoc();
$stmt->close();

if (!$alumno) {
    header('Location: index.php');
    exit;
}

$mensaje = '';
$tipo_mensaje = '';

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre         = trim($_POST['nombre'] ?? '');
    $identificacion = trim($_POST['identificacion'] ?? '');
    $telefono       = trim($_POST['telefono'] ?? '');

    if ($nombre === '' || $identificacion === '') {
        $mensaje = 'El nombre y la identificación son obligatorios.';
        $tipo_mensaje = 'warning';
    } else {
        $stmt = $conn->prepare("UPDATE alumnos SET nombre = ?, identificacion = ?, telefono = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $identificacion, $telefono, $id);
        if ($stmt->execute()) {
            header('Location: index.php?msg=updated');
            exit;
        } else {
            $mensaje = 'Error al actualizar el alumno.';
            $tipo_mensaje = 'danger';
        }
        $stmt->close();
    }
    // Mantener valores enviados en caso de error
    $alumno['nombre']         = $nombre;
    $alumno['identificacion'] = $identificacion;
    $alumno['telefono']       = $telefono;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Alumno #<?php echo $id; ?> | Academia Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <i class="bi bi-mortarboard-fill fs-3"></i>
                <span>Academia Pro</span>
            </a>
        </div>
    </nav>

    <main class="container py-4">
        <div class="mb-4">
            <a href="index.php" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
            <h1 class="h3 mt-2 fw-bold">Editar Alumno #<?php echo $id; ?></h1>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo e($tipo_mensaje); ?> shadow-sm">
                <?php echo e($mensaje); ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-pencil-square text-primary me-2"></i>Datos del alumno
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="edit.php?id=<?php echo $id; ?>">
                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-medium">Nombre completo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                           required maxlength="255" value="<?php echo e($alumno['nombre']); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="identificacion" class="form-label fw-medium">Identificación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control" id="identificacion" name="identificacion"
                                           required maxlength="50" value="<?php echo e($alumno['identificacion']); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="telefono" class="form-label fw-medium">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control" id="telefono" name="telefono"
                                           maxlength="30" value="<?php echo e($alumno['telefono']); ?>">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1 py-2 fw-semibold">
                                    <i class="bi bi-check2-circle me-2"></i>Guardar cambios
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary py-2">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-top mt-5 py-4">
        <div class="container text-center text-muted small">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Academia Pro</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
