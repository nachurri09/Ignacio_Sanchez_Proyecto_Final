<?php
require 'conexion.php';
require 'includes/funciones.php';

$productos = $pdo->query('SELECT id, nombre, precio, stock, imagen FROM productos ORDER BY id')->fetchAll();

$titulo = 'Productos';
require 'includes/header.php';
?>
<h1 class="titulo-centrado">Nuestras opciones de compra</h1>

<?php if (!$productos): ?>
    <p class="titulo-centrado">Todavía no hay productos cargados.</p>
<?php endif; ?>

<div class="grid-productos">
<?php foreach ($productos as $p): ?>
    <article class="producto">
        <a href="producto.php?id=<?= (int)$p['id'] ?>"><?= img_producto($p['imagen'], $p['nombre']) ?></a>
        <div class="producto-cuerpo">
            <h2><?= e($p['nombre']) ?></h2>
            <p class="precio"><?= e(precio($p['precio'])) ?></p>
            <div class="acciones">
                <a class="boton boton-claro boton-chico" href="producto.php?id=<?= (int)$p['id'] ?>">Ver detalle</a>
                <?php if ((int)$p['stock'] > 0): ?>
                <form method="post" action="carrito_accion.php">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="accion" value="agregar">
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <input type="hidden" name="cantidad" value="1">
                    <input type="hidden" name="volver" value="productos">
                    <button class="boton boton-chico" type="submit">Agregar al carrito</button>
                </form>
                <?php else: ?>
                    <span class="etiqueta etiqueta-sinstock">Sin stock</span>
                <?php endif; ?>
            </div>
        </div>
    </article>
<?php endforeach; ?>
</div>
<?php require 'includes/footer.php'; ?>
