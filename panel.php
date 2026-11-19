<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_login();

// Nunca se selecciona la contraseña: no hace falta para mostrar datos.
$s = $pdo->prepare('SELECT nombre, apellido, dni, email, rol, fecha_creacion
                    FROM usuarios WHERE id = ? AND fecha_archivado IS NULL');
$s->execute([$_SESSION['usuario_id']]);
$u = $s->fetch();

if (!$u) { // cuenta archivada mientras estaba logueado
    session_destroy();
    session_start();
    flash('error', 'Tu cuenta ya no está activa.');
    header('Location: login.php');
    exit;
}

$titulo = 'Mi panel';
require 'includes/header.php';
?>
<section class="tarjeta tarjeta-form">
    <h1>Hola, <?= e($u['nombre']) ?></h1>
    <dl class="datos">
        <dt>Nombre completo</dt><dd><?= e($u['nombre'] . ' ' . $u['apellido']) ?></dd>
        <dt>DNI</dt><dd><?= e($u['dni']) ?></dd>
        <dt>Correo electrónico</dt><dd><?= e($u['email']) ?></dd>
        <dt>Tipo de cuenta</dt><dd><?= $u['rol'] === 'admin' ? 'Administrador' : 'Comprador' ?></dd>
        <dt>Cuenta creada</dt><dd><?= e(date('d/m/Y H:i', strtotime($u['fecha_creacion']))) ?></dd>
    </dl>
    <div class="acciones">
        <a class="boton" href="productos.php">Ver productos</a>
        <a class="boton boton-claro" href="carrito.php">Mi carrito (<?= cantidad_carrito() ?>)</a>
        <?php if ($u['rol'] === 'admin'): ?>
            <a class="boton boton-claro" href="admin_productos.php">Gestionar productos</a>
            <a class="boton boton-claro" href="admin_usuarios.php">Gestionar usuarios</a>
        <?php endif; ?>
    </div>
</section>
<?php require 'includes/footer.php'; ?>
