<?php
declare(strict_types=1);

session_start();
require __DIR__ . '/database.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirectWithMessage(string $type, string $message): never
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    header('Location: index.php');
    exit;
}

function validCsrf(): bool
{
    return isset($_POST['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token']);
}

$connectionError = null;
$students = [];

try {
    $pdo = database();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!validCsrf()) {
            redirectWithMessage('error', 'La sesión venció. Intenta nuevamente.');
        }

        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'create' || $action === 'update') {
            $cedula = trim((string) ($_POST['cedula'] ?? ''));
            $originalCedula = trim((string) ($_POST['original_cedula'] ?? ''));
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $telefono = trim((string) ($_POST['telefono'] ?? ''));

            if ($cedula === '' || $nombre === '' || $telefono === '') {
                redirectWithMessage('error', 'Todos los campos son obligatorios.');
            }

            if (!preg_match('/^[0-9]{5,20}$/', $cedula)) {
                redirectWithMessage('error', 'La cédula debe contener entre 5 y 20 dígitos.');
            }

            if (mb_strlen($nombre) > 120 || mb_strlen($telefono) > 30) {
                redirectWithMessage('error', 'Verifica la longitud de los datos ingresados.');
            }

            if ($action === 'create') {
                $statement = $pdo->prepare(
                    'INSERT INTO alumnos (cedula, nombre, telefono) VALUES (:cedula, :nombre, :telefono)'
                );
                $statement->execute(compact('cedula', 'nombre', 'telefono'));
                redirectWithMessage('success', 'Alumno registrado correctamente.');
            }

            if ($originalCedula === '') {
                redirectWithMessage('error', 'No fue posible identificar el alumno a editar.');
            }

            $statement = $pdo->prepare(
                'UPDATE alumnos SET cedula = :cedula, nombre = :nombre, telefono = :telefono '
                . 'WHERE cedula = :original_cedula'
            );
            $statement->execute([
                'cedula' => $cedula,
                'nombre' => $nombre,
                'telefono' => $telefono,
                'original_cedula' => $originalCedula,
            ]);
            redirectWithMessage('success', 'Datos del alumno actualizados.');
        }

        if ($action === 'delete') {
            $cedula = trim((string) ($_POST['cedula'] ?? ''));
            if ($cedula === '') {
                redirectWithMessage('error', 'No fue posible identificar el alumno.');
            }

            $statement = $pdo->prepare('DELETE FROM alumnos WHERE cedula = :cedula');
            $statement->execute(['cedula' => $cedula]);
            redirectWithMessage('success', 'Alumno eliminado correctamente.');
        }

        redirectWithMessage('error', 'Acción no reconocida.');
    }

    $search = trim((string) ($_GET['q'] ?? ''));
    if ($search !== '') {
        $statement = $pdo->prepare(
            'SELECT cedula, nombre, telefono FROM alumnos '
            . 'WHERE cedula LIKE :search OR nombre LIKE :search OR telefono LIKE :search '
            . 'ORDER BY nombre ASC'
        );
        $statement->execute(['search' => '%' . $search . '%']);
        $students = $statement->fetchAll();
    } else {
        $students = $pdo->query(
            'SELECT cedula, nombre, telefono FROM alumnos ORDER BY nombre ASC'
        )->fetchAll();
    }
} catch (PDOException $exception) {
    if ((int) $exception->getCode() === 23000) {
        redirectWithMessage('error', 'Ya existe un alumno con esa cédula.');
    }
    $connectionError = 'No fue posible conectar con la base de datos. Verifica la configuración y que la tabla haya sido creada.';
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$search = trim((string) ($_GET['q'] ?? ''));
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071d48">
    <title>Gestión de Alumnos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="background-shade"></div>
    <header class="topbar">
        <a class="brand" href="index.php" aria-label="Ir al inicio">
            <img src="assets/img/gestionLogo.png" alt="Gestión Alumnos">
            <div>
                <span>Plataforma académica</span>
                <strong>Gestión de alumnos</strong>
            </div>
        </a>
        <button class="primary-button" type="button" data-open-modal>
            <span aria-hidden="true">＋</span> Nuevo alumno
        </button>
    </header>

    <main class="page-shell">
        <section class="hero">
            <div>
                <span class="eyebrow">Panel administrativo</span>
                <h1>Control de estudiantes<br><em>simple y eficiente.</em></h1>
                <p>Administra la información de tus alumnos desde un solo lugar.</p>
            </div>
            <div class="stat-card">
                <span class="stat-icon" aria-hidden="true">♙</span>
                <div>
                    <strong><?= count($students) ?></strong>
                    <span><?= $search !== '' ? 'Resultados encontrados' : 'Alumnos registrados' ?></span>
                </div>
            </div>
        </section>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>" role="status">
                <span><?= $flash['type'] === 'success' ? '✓' : '!' ?></span>
                <?= e($flash['message']) ?>
                <button type="button" aria-label="Cerrar mensaje" data-close-alert>×</button>
            </div>
        <?php endif; ?>

        <?php if ($connectionError): ?>
            <div class="alert alert-error" role="alert">
                <span>!</span><?= e($connectionError) ?>
            </div>
        <?php endif; ?>

        <section class="data-card" aria-labelledby="student-list-title">
            <div class="card-header">
                <div>
                    <span class="section-kicker">Directorio</span>
                    <h2 id="student-list-title">Listado de alumnos</h2>
                </div>
                <form class="search-box" method="get" action="index.php" role="search">
                    <span aria-hidden="true">⌕</span>
                    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Buscar por nombre, cédula o teléfono" aria-label="Buscar alumnos">
                    <?php if ($search !== ''): ?>
                        <a href="index.php" aria-label="Limpiar búsqueda">×</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if (!$connectionError && count($students) > 0): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Cédula</th>
                                <th>Teléfono</th>
                                <th class="actions-heading">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td data-label="Alumno">
                                        <div class="student-cell">
                                            <span class="avatar"><?= e(mb_strtoupper(mb_substr($student['nombre'], 0, 1))) ?></span>
                                            <strong><?= e($student['nombre']) ?></strong>
                                        </div>
                                    </td>
                                    <td data-label="Cédula"><span class="id-badge"><?= e($student['cedula']) ?></span></td>
                                    <td data-label="Teléfono"><?= e($student['telefono']) ?></td>
                                    <td data-label="Acciones">
                                        <div class="row-actions">
                                            <button type="button" class="icon-button edit-button"
                                                data-edit
                                                data-cedula="<?= e($student['cedula']) ?>"
                                                data-nombre="<?= e($student['nombre']) ?>"
                                                data-telefono="<?= e($student['telefono']) ?>"
                                                aria-label="Editar a <?= e($student['nombre']) ?>" title="Editar">✎</button>
                                            <form method="post" action="index.php" data-delete-form data-student="<?= e($student['nombre']) ?>">
                                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="cedula" value="<?= e($student['cedula']) ?>">
                                                <button type="submit" class="icon-button delete-button" aria-label="Eliminar a <?= e($student['nombre']) ?>" title="Eliminar">⌫</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (!$connectionError): ?>
                <div class="empty-state">
                    <div aria-hidden="true">♙</div>
                    <h3><?= $search !== '' ? 'Sin resultados' : 'Aún no hay alumnos' ?></h3>
                    <p><?= $search !== '' ? 'Prueba con otro término de búsqueda.' : 'Registra el primer alumno para comenzar.' ?></p>
                    <?php if ($search === ''): ?>
                        <button class="secondary-button" type="button" data-open-modal>Agregar alumno</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>Gestión de Alumnos · Plataforma académica</footer>

    <dialog class="modal" id="student-modal" aria-labelledby="modal-title">
        <form method="post" action="index.php" id="student-form">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" id="form-action" value="create">
            <input type="hidden" name="original_cedula" id="original-cedula" value="">
            <div class="modal-heading">
                <div>
                    <span class="section-kicker" id="modal-kicker">Nuevo registro</span>
                    <h2 id="modal-title">Agregar alumno</h2>
                </div>
                <button type="button" class="modal-close" data-close-modal aria-label="Cerrar">×</button>
            </div>
            <div class="form-grid">
                <label>
                    <span>Cédula</span>
                    <input type="text" inputmode="numeric" name="cedula" id="cedula" maxlength="20" pattern="[0-9]{5,20}" placeholder="Ej. 1020304050" required>
                    <small>Entre 5 y 20 dígitos</small>
                </label>
                <label>
                    <span>Nombre completo</span>
                    <input type="text" name="nombre" id="nombre" maxlength="120" placeholder="Ej. Laura Martínez" required>
                </label>
                <label class="full-width">
                    <span>Teléfono</span>
                    <input type="tel" name="telefono" id="telefono" maxlength="30" placeholder="Ej. 300 123 4567" required>
                </label>
            </div>
            <div class="modal-actions">
                <button type="button" class="cancel-button" data-close-modal>Cancelar</button>
                <button type="submit" class="primary-button" id="submit-label">Guardar alumno</button>
            </div>
        </form>
    </dialog>

    <script src="assets/js/app.js"></script>
</body>
</html>
