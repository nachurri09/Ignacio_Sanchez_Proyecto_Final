<?php
require 'conexion.php';
require 'includes/funciones.php';

$carrito = $_SESSION['carrito'] ?? [];
$items = [];
$total = 0;

if ($carrito) {
    $ids = array_map('intval', array_keys($carrito));
    $marcas = implode(',', array_fill(0, count($ids), '?'));
    $s = $pdo->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE id IN ($marcas)");
    $s->execute($ids);
    $nuevo = [];
    foreach ($s->fetchAll() as $p) {
        $cant = (int)$carrito[$p['id']];
        $p['cantidad'] = $cant;
        $p['subtotal'] = $cant * (int)$p['precio'];
        $total += $p['subtotal'];
        $items[] = $p;
        $nuevo[$p['id']] = $cant;
    }
    $_SESSION['carrito'] = $nuevo; // saca del carrito lo que ya no existe
}

$titulo = 'Carrito';
require 'includes/header.php';
?>
<section class="tarjeta">
    <h1>Mi carrito</h1>

    <?php if (!$items): ?>
        <p class="vacio">Tu carrito está vacío. <a href="productos.php">Ver productos</a></p>
    <?php else: ?>
    <div class="tabla-scroll"><table>
        <thead><tr><th></th><th>Producto</th><th>Precio</th><th>Cant.</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($items as $x): ?>
            <tr>
                <td style="width:70px"><?= img_producto($x['imagen'], $x['nombre'], 'img-mini') ?></td>
                <td><a href="producto.php?id=<?= (int)$x['id'] ?>"><?= e($x['nombre']) ?></a></td>
                <td><?= e(precio($x['precio'])) ?></td>
                <td><?= (int)$x['cantidad'] ?></td>
                <td><?= e(precio($x['subtotal'])) ?></td>
                <td>
                    <form method="post" action="carrito_accion.php">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= (int)$x['id'] ?>">
                        <input type="hidden" name="volver" value="carrito">
                        <button class="boton boton-chico boton-peligro" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>

    <p class="total">Total: <?= e(precio($total)) ?></p>
    <div class="acciones">
        <a class="boton boton-claro" href="productos.php">Seguir comprando</a>
        <form method="post" action="carrito_accion.php" onsubmit="return confirm('¿Vaciar el carrito?');">
            <?= csrf_campo() ?>
            <input type="hidden" name="accion" value="vaciar">
            <input type="hidden" name="volver" value="carrito">
            <button class="boton boton-peligro" type="submit">Vaciar carrito</button>
        </form>
    </div>
    <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
