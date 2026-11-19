<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: admin_usuarios.php'); exit; }
csrf_validar();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    flash('error', 'Usuario no válido.');
} elseif ($id === (int)$_SESSION['usuario_id']) {
    flash('error', 'No podés archivar tu propia cuenta.');
} else {
    // No se borra: solo se marca la fecha de archivado.
    $s = $pdo->prepare('UPDATE usuarios SET fecha_archivado = NOW() WHERE id = ? AND fecha_archivado IS NULL');
    $s->execute([$id]);
    flash($s->rowCount() ? 'ok' : 'error', $s->rowCount() ? 'Usuario archivado.' : 'No se encontró el usuario.');
}
header('Location: admin_usuarios.php');
exit;
