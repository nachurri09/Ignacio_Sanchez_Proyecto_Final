<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

$usuarios = $pdo->query('SELECT id, nombre, apellido, dni, email, rol, fecha_archivado
                         FROM usuarios WHERE fecha_archivado IS NOT NULL
                         ORDER BY fecha_archivado DESC')->fetchAll();

$titulo = 'Usuarios archivados';
require 'includes/header.php';
?>
<section class="tarjeta">
    <div class="encabezado">
        <h1>Usuarios archivados</h1>
        <a class="boton boton-claro" href="admin_usuarios.php">Volver a activos</a>
    </div>

    <?php if (!$usuarios): ?>
        <p class="vacio">No hay usuarios archivados.</p>
    <?php else: ?>
    <div class="tabla-scroll">
    <table>
        <thead><tr><th>Apellido y nombre</th><th>DNI</th><th>Correo</th><th>Archivado el</th><th>Acción</th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= e($u['apellido'] . ', ' . $u['nombre']) ?></td>
                <td><?= e($u['dni']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e(date('d/m/Y H:i', strtotime($u['fecha_archivado']))) ?></td>
                <td>
                    <form method="post" action="reactivar_usuario.php">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                        <button class="boton boton-chico" type="submit">Reactivar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
