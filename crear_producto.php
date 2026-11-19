<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

$errores = [];
$v = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $v = datos_producto();
    $errores = validar_producto($v);
    $imagen = $errores ? null : subir_imagen($_FILES['imagen'] ?? [], $errores);

    if (!$errores) {
        $s = $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?,?,?,?,?)');
        $s->execute([$v['nombre'], $v['descripcion'], (int)$v['precio'], (int)$v['stock'], $imagen]);
        flash('ok', 'Producto agregado correctamente.');
        header('Location: admin_productos.php');
        exit;
    }
}

$titulo = 'Agregar producto';
require 'includes/header.php';
?>
<section class="tarjeta tarjeta-form">
    <h1>Agregar producto</h1>
    <?php mostrar_errores($errores); ?>
    <form method="post" enctype="multipart/form-data" novalidate>
        <?= csrf_campo() ?>
        <?php require 'includes/campos_producto.php'; ?>
        <div class="acciones">
            <button class="boton" type="submit">Guardar</button>
            <a class="boton boton-claro" href="admin_productos.php">Cancelar</a>
        </div>
    </form>
</section>
<?php require 'includes/footer.php'; ?>
