<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

$id = filter_var($_GET['id'] ?? $_POST['id'] ?? null, FILTER_VALIDATE_INT);
$p = false;
if ($id) {
    $s = $pdo->prepare('SELECT id, nombre, descripcion, precio, stock, imagen FROM productos WHERE id = ?');
    $s->execute([$id]);
    $p = $s->fetch();
}
if (!$p) { flash('error', 'El producto no existe.'); header('Location: admin_productos.php'); exit; }

$errores = [];
$v = $p;
$imagenActual = $p['imagen'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $v = datos_producto();
    $errores = validar_producto($v);
    $nueva = $errores ? null : subir_imagen($_FILES['imagen'] ?? [], $errores);

    if (!$errores) {
        $imagen = $nueva ?? $p['imagen'];
        $s = $pdo->prepare('UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, imagen=? WHERE id=?');
        $s->execute([$v['nombre'], $v['descripcion'], (int)$v['precio'], (int)$v['stock'], $imagen, $id]);
        if ($nueva) borrar_imagen($p['imagen']); // saca la imagen vieja si la había subido el panel
        flash('ok', 'Producto actualizado.');
        header('Location: admin_productos.php');
        exit;
    }
}

$titulo = 'Editar producto';
require 'includes/header.php';
?>
<section class="tarjeta tarjeta-form">
    <h1>Editar producto</h1>
    <?php mostrar_errores($errores); ?>
    <form method="post" enctype="multipart/form-data" novalidate>
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= (int)$id ?>">
        <?php require 'includes/campos_producto.php'; ?>
        <div class="acciones">
            <button class="boton" type="submit">Guardar cambios</button>
            <a class="boton boton-claro" href="admin_productos.php">Cancelar</a>
        </div>
    </form>
</section>
<?php require 'includes/footer.php'; ?>
