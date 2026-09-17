<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

function fail(string $message): never {
    header('Location: ../index.php?msg=' . urlencode($message)); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../index.php'); exit; }

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nombre = trim($_POST['nombre'] ?? '');
$identificacion = trim($_POST['identificacion'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
if (!$id || $nombre === '' || $identificacion === '' || $telefono === '') fail('Datos inválidos.');

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT imagen FROM alumnos WHERE id = ?");
$stmt->execute([$id]);
$actual = $stmt->fetch();
if (!$actual) fail('Alumno no encontrado.');

$nuevaImagen = $actual['imagen'];
$nuevoArchivoFisico = null;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) fail('No fue posible recibir la nueva fotografía.');
    $file = $_FILES['imagen'];
    if ($file['size'] > 5 * 1024 * 1024) fail('La imagen supera el tamaño máximo de 5 MB.');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) fail('La nueva fotografía no es válida.');
    $nuevaImagen = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $dir = __DIR__ . '/../uploads/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $dir . $nuevaImagen)) fail('No fue posible guardar la nueva fotografía.');
    $nuevoArchivoFisico = $dir . $nuevaImagen;
}

try {
    $stmt = $pdo->prepare("UPDATE alumnos SET nombre=?, identificacion=?, telefono=?, imagen=? WHERE id=?");
    $stmt->execute([$nombre, $identificacion, $telefono, $nuevaImagen, $id]);
    if ($nuevoArchivoFisico) {
        $old = realpath(__DIR__ . '/../uploads/' . basename($actual['imagen']));
        $base = realpath(__DIR__ . '/../uploads/');
        if ($old && $base && str_starts_with($old, $base . DIRECTORY_SEPARATOR) && is_file($old)) @unlink($old);
    }
    fail('Alumno actualizado correctamente.');
} catch (Throwable $e) {
    if ($nuevoArchivoFisico) @unlink($nuevoArchivoFisico);
    fail('No fue posible actualizar el alumno.');
}
