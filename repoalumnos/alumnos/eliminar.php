<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../index.php'); exit; }
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: ../index.php?msg=' . urlencode('Identificador inválido.')); exit; }

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT imagen FROM alumnos WHERE id = ?");
$stmt->execute([$id]);
$alumno = $stmt->fetch();

if ($alumno) {
    $stmt = $pdo->prepare("DELETE FROM alumnos WHERE id = ?");
    $stmt->execute([$id]);
    $file = realpath(__DIR__ . '/../uploads/' . basename($alumno['imagen']));
    $base = realpath(__DIR__ . '/../uploads/');
    if ($file && $base && str_starts_with($file, $base . DIRECTORY_SEPARATOR) && is_file($file)) @unlink($file);
}
header('Location: ../index.php?msg=' . urlencode('Alumno eliminado correctamente.'));
exit;
