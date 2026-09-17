<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/init.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $pdo = getPDO();
    initializeDatabase($pdo);

    $stmt = $pdo->prepare('SELECT imagen FROM alumnos WHERE id = ?');
    $stmt->execute([$id]);
    $alumno = $stmt->fetch();

    if ($alumno) {
        $stmt = $pdo->prepare('DELETE FROM alumnos WHERE id = ?');
        $stmt->execute([$id]);

        if (!empty($alumno['imagen']) && is_file(__DIR__ . '/' . $alumno['imagen'])) {
            @unlink(__DIR__ . '/' . $alumno['imagen']);
        }
    }

    header('Location: index.php');
    exit;
} catch (Throwable $e) {
    die('Error al eliminar: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
