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

    $stmt = $pdo->prepare('SELECT * FROM alumnos WHERE id = ?');
    $stmt->execute([$id]);
    $alumno = $stmt->fetch();

    if (!$alumno) {
        header('Location: index.php');
        exit;
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $identificacion = trim($_POST['identificacion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nombre === '' || $identificacion === '' || $telefono === '') {
        die('Todos los campos son obligatorios.');
    }

    $imagen = $alumno['imagen'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            die('Error al subir la nueva imagen.');
        }

        if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            die('La imagen no puede superar 5 MB.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['imagen']['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed[$mime])) {
            die('Solo se permiten imágenes JPG, PNG o WEBP.');
        }

        $newImage = 'alumno_' . bin2hex(random_bytes(16)) . '.' . $allowed[$mime];

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__ . '/' . $newImage)) {
            die('No fue posible guardar la nueva imagen.');
        }

        if (is_file(__DIR__ . '/' . $imagen)) {
            @unlink(__DIR__ . '/' . $imagen);
        }

        $imagen = $newImage;
    }

    $stmt = $pdo->prepare(
        'UPDATE alumnos SET nombre = ?, identificacion = ?, telefono = ?, imagen = ? WHERE id = ?'
    );
    $stmt->execute([$nombre, $identificacion, $telefono, $imagen, $id]);

    header('Location: index.php');
    exit;
} catch (Throwable $e) {
    die('Error al actualizar: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
