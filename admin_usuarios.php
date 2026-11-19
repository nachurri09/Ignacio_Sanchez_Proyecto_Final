<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

$q = trim($_GET['q'] ?? '');
$sql = 'SELECT id, nombre, apellido, dni, email, rol, fecha_creacion
        FROM usuarios WHERE fecha_archivado IS NULL';
$params = [];
if ($q !== '') {
    $sql .= ' AND (nombre LIKE :q OR apellido LIKE :q OR dni LIKE :q OR email LIKE :q)';
    $params[':q'] = '%' . $q . '%';
}
$sql .= ' ORDER BY apellido, nombre';
$s = $pdo->prepare($sql);
$s->execute($params);
$usuarios = $s->fetchAll();

$titulo = 'Usuarios';
require 'includes/header.php';
?>
<section class="tarjeta">
    <div class="encabezado">
        <h1>Usuarios activos</h1>
        <a class="boton" href="crear_usuario.php">Registrar usuario</a>
    </div>

    <form class="buscador" method="get">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, apellido, DNI o correo">
        <button class="boton" type="submit">Buscar</button>
        <?php if ($q !== ''): ?><a class="boton boton-claro" href="admin_usuarios.php">Limpiar</a><?php endif; ?>
    </form>

    <?php if (!$usuarios): ?>
        <p class="vacio"><?= $q !== '' ? 'No hay usuarios que coincidan con la búsqueda.' : 'Todavía no hay usuarios activos.' ?></p>
    <?php else: ?>
    <div class="tabla-scroll">
    <table>
        <thead><tr><th>Apellido y nombre</th><th>DNI</th><th>Correo</th><th>Rol</th><th>Alta</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= e($u['apellido'] . ', ' . $u['nombre']) ?></td>
                <td><?= e($u['dni']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><span class="etiqueta etiqueta-<?= e($u['rol']) ?>"><?= $u['rol'] === 'admin' ? 'Admin' : 'Comprador' ?></span></td>
                <td><?= e(date('d/m/Y', strtotime($u['fecha_creacion']))) ?></td>
                <td class="acciones-fila">
                    <a class="boton boton-chico boton-claro" href="editar_usuario.php?id=<?= (int)$u['id'] ?>">Editar</a>
                    <?php if ((int)$u['id'] !== (int)$_SESSION['usuario_id']): ?>
                    <form method="post" action="archivar_usuario.php"
                          onsubmit="return confirm('¿Archivar a este usuario? Podrás reactivarlo después.');">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                        <button class="boton boton-chico boton-peligro" type="submit">Archivar</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <p class="nota"><?= count($usuarios) ?> usuario(s)</p>
    <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
