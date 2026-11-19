<?php
// Agrega o saca productos del carrito. El carrito vive en la sesión: $_SESSION['carrito'][id] = cantidad
require 'conexion.php';
require 'includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: productos.php'); exit; }
csrf_validar();

$accion = $_POST['accion'] ?? '';
$id     = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$_SESSION['carrito'] = $_SESSION['carrito'] ?? [];

if ($accion === 'agregar') {
    $cant = filter_var($_POST['cantidad'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]);
    if (!$id || !$cant) {
        flash('error', 'Datos no válidos.');
    } else {
        $s = $pdo->prepare('SELECT stock FROM productos WHERE id = ?');
        $s->execute([$id]);
        $p = $s->fetch();
        $actual = (int)($_SESSION['carrito'][$id] ?? 0);

        if (!$p)                                   flash('error', 'El producto no existe.');
        elseif ($actual + $cant > (int)$p['stock']) flash('error', 'No hay suficiente stock para agregar esa cantidad.');
        else {
            $_SESSION['carrito'][$id] = $actual + $cant;
            flash('ok', 'Producto agregado al carrito.');
        }
    }
} elseif ($accion === 'eliminar' && $id) {
    unset($_SESSION['carrito'][$id]);
    flash('ok', 'Producto eliminado del carrito.');
} elseif ($accion === 'vaciar') {
    $_SESSION['carrito'] = [];
    flash('ok', 'Carrito vaciado.');
}

// Volver a la página desde donde se hizo la acción (lista cerrada, no se confía en el formulario)
$volver = $_POST['volver'] ?? '';
if ($volver === 'carrito')                  $destino = 'carrito.php';
elseif ($volver === 'producto' && $id)      $destino = 'producto.php?id=' . $id;
else                                        $destino = 'productos.php';
header('Location: ' . $destino);
exit;
