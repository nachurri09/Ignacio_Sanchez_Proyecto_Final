<?php /* Uso: $titulo = '...'; require 'includes/header.php'; */
$logueado = !empty($_SESSION['usuario_id']);
$esAdmin  = ($_SESSION['rol'] ?? '') === 'admin';
$nCarrito = cantidad_carrito();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Inicio') ?> · <?= e(SITIO) ?></title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="barra">
    <div class="barra-in">
        <a class="marca" href="index.php"><img src="img/logo.svg" alt="" width="34" height="34"><span><?= e(SITIO) ?></span></a>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="carrito.php">Carrito (<?= $nCarrito ?>)</a>
            <?php if (!$logueado): ?>
                <a class="nav-destacado" href="login.php">Ingresar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<?php if ($logueado): ?>
<div class="subbarra"><div class="barra-in">
    <span>Hola, <?= e($_SESSION['nombre'] ?? '') ?> · <?= $esAdmin ? 'Administrador' : 'Comprador' ?></span>
    <nav>
        <a href="panel.php">Mi panel</a>
        <?php if ($esAdmin): ?>
            <a href="admin_productos.php">Gestionar productos</a>
            <a href="admin_usuarios.php">Usuarios</a>
            <a href="usuarios_archivados.php">Archivados</a>
        <?php endif; ?>
        <a href="cerrar_sesion.php">Cerrar sesión</a>
    </nav>
</div></div>
<?php endif; ?>
<main class="contenedor">
<?php mostrar_flash(); ?>
