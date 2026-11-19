<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: admin_productos.php'); exit; }
csrf_validar();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    flash('error', 'Producto no válido.');
} else {
    $s = $pdo->prepare('SELECT imagen FROM productos WHERE id = ?');
    $s->execute([$id]);
    $p = $s->fetch();

    if (!$p) {
        flash('error', 'El producto no existe.');
    } else {
        $pdo->prepare('DELETE FROM productos WHERE id = ?')->execute([$id]);
        borrar_imagen($p['imagen']);
        unset($_SESSION['carrito'][$id]); // por si estaba en el carrito
        flash('ok', 'Producto eliminado.');
    }
}
header('Location: admin_productos.php');
exit;
