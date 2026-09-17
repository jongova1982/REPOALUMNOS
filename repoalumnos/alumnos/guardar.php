<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../database/init.php';

function redirectError(string $message): never {
    header('Location: crear.php?error=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../index.php'); exit; }

$nombre = trim($_POST['nombre'] ?? '');
$identificacion = trim($_POST['identificacion'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if ($nombre === '' || $identificacion === '' || $telefono === '') redirectError('Todos los campos son obligatorios.');
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) redirectError('Debe seleccionar una fotografía.');

$file = $_FILES['imagen'];
if ($file['size'] > 5 * 1024 * 1024) redirectError('La imagen supera el tamaño máximo de 5 MB.');

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) redirectError('El archivo no es una imagen válida.');

$filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) redirectError('No fue posible guardar la fotografía.');

try {
    $pdo = getPDO();
    initializeDatabase($pdo);
    $stmt = $pdo->prepare("INSERT INTO alumnos (nombre, identificacion, telefono, imagen) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $identificacion, $telefono, $filename]);
    header('Location: ../index.php?msg=' . urlencode('Alumno registrado correctamente.'));
} catch (Throwable $e) {
    @unlink($uploadDir . $filename);
    redirectError('No fue posible registrar el alumno.');
}
exit;
