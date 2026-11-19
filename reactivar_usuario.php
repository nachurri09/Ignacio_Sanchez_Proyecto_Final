<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: usuarios_archivados.php'); exit; }
csrf_validar();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    flash('error', 'Usuario no válido.');
} else {
    $s = $pdo->prepare('UPDATE usuarios SET fecha_archivado = NULL WHERE id = ? AND fecha_archivado IS NOT NULL');
    $s->execute([$id]);
    flash($s->rowCount() ? 'ok' : 'error', $s->rowCount() ? 'Usuario reactivado.' : 'No se encontró el usuario archivado.');
}
header('Location: usuarios_archivados.php');
exit;
