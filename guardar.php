<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/init.php';

function fail(string $message): never
{
    header('Location: crear.php?error=' . rawurlencode($message));
    exit;
}

try {
    $pdo = getPDO();
    initializeDatabase($pdo);

    $nombre = trim($_POST['nombre'] ?? '');
    $identificacion = trim($_POST['identificacion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nombre === '' || $identificacion === '' || $telefono === '') {
        fail('Todos los campos son obligatorios.');
    }

    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        fail('La imagen es obligatoria.');
    }

    if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
        fail('La imagen no puede superar 5 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['imagen']['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        fail('Solo se permiten imágenes JPG, PNG o WEBP.');
    }

    $filename = 'alumno_' . bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $destination = __DIR__ . '/' . $filename;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destination)) {
        fail('No fue posible guardar la imagen.');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO alumnos (nombre, identificacion, telefono, imagen) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$nombre, $identificacion, $telefono, $filename]);

    header('Location: index.php');
    exit;
} catch (Throwable $e) {
    fail('Error al guardar: ' . $e->getMessage());
}
