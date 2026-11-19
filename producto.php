<?php
require 'conexion.php';
require 'includes/funciones.php';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$p = false;
if ($id) {
    $s = $pdo->prepare('SELECT id, nombre, descripcion, precio, stock, imagen FROM productos WHERE id = ?');
    $s->execute([$id]);
    $p = $s->fetch();
}
if (!$p) {
    flash('error', 'El producto no existe.');
    header('Location: productos.php');
    exit;
}

$titulo = $p['nombre'];
require 'includes/header.php';
?>
<section class="tarjeta detalle">
    <div><?= img_producto($p['imagen'], $p['nombre'], 'img-detalle') ?></div>
    <div>
        <h1><?= e($p['nombre']) ?></h1>
        <p class="precio"><?= e(precio($p['precio'])) ?></p>
        <p><?= $p['descripcion'] !== '' ? nl2br(e($p['descripcion'])) : 'Sin descripción.' ?></p>
        <p class="nota">Stock disponible: <?= (int)$p['stock'] ?></p>

        <?php if ((int)$p['stock'] > 0): ?>
        <form method="post" action="carrito_accion.php" class="comprar">
            <?= csrf_campo() ?>
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <input type="hidden" name="volver" value="producto">
            <label>Cantidad
                <input type="number" name="cantidad" min="1" max="<?= min(10, (int)$p['stock']) ?>" value="1" required>
            </label>
            <button class="boton" type="submit">Agregar al carrito</button>
        </form>
        <?php else: ?>
            <span class="etiqueta etiqueta-sinstock">Sin stock</span>
        <?php endif; ?>
        <p style="margin-top:1rem"><a href="productos.php">&larr; Volver a los productos</a></p>
    </div>
</section>
<?php require 'includes/footer.php'; ?>
