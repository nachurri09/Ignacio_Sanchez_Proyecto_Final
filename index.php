<?php
require 'includes/funciones.php';
$titulo = 'Inicio';
require 'includes/header.php';
?>
<section class="hero">
    <div>
        <h1>Entradas para tus eventos favoritos</h1>
        <p>Elegí entre nuestras opciones de compra, mirá el detalle de cada una y agregalas a tu carrito.</p>
        <a class="boton" href="productos.php">Ver productos</a>
    </div>
    <img src="img/entradas.svg" alt="Entradas">
</section>

<div class="banda">
    <div class="banda-in">
        <ul class="check-lista">
            <li>Mirá las <strong>6 opciones de compra</strong> y entrá al detalle de cada una.</li>
            <li>Agregá lo que quieras al <strong>carrito</strong> y sacá lo que no quieras.</li>
            <li>Creá tu cuenta para ingresar como <strong>comprador</strong>.</li>
        </ul>
        <p class="centro">
            <?php if (empty($_SESSION['usuario_id'])): ?>
                <a class="boton" href="registro.php">Crear cuenta</a>
            <?php else: ?>
                <a class="boton" href="panel.php">Ir a mi panel</a>
            <?php endif; ?>
        </p>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
