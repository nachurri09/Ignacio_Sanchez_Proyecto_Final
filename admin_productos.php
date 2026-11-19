<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

$productos = $pdo->query('SELECT id, nombre, precio, stock, imagen FROM productos ORDER BY id')->fetchAll();

$titulo = 'Gestionar productos';
require 'includes/header.php';
?>
<section class="tarjeta">
    <div class="encabezado">
        <h1>Productos</h1>
        <a class="boton" href="crear_producto.php">Agregar producto</a>
    </div>

    <?php if (!$productos): ?>
        <p class="vacio">No hay productos cargados.</p>
    <?php else: ?>
    <div class="tabla-scroll"><table>
        <thead><tr><th></th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td style="width:70px"><?= img_producto($p['imagen'], $p['nombre'], 'img-mini') ?></td>
                <td><?= e($p['nombre']) ?></td>
                <td><?= e(precio($p['precio'])) ?></td>
                <td><?= (int)$p['stock'] ?></td>
                <td class="acciones-fila">
                    <a class="boton boton-chico boton-claro" href="editar_producto.php?id=<?= (int)$p['id'] ?>">Editar</a>
                    <form method="post" action="eliminar_producto.php" onsubmit="return confirm('¿Eliminar este producto definitivamente?');">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="boton boton-chico boton-peligro" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
