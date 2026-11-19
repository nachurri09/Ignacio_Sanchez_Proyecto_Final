<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

$id = filter_var($_GET['id'] ?? $_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) { flash('error', 'Usuario no válido.'); header('Location: admin_usuarios.php'); exit; }

$s = $pdo->prepare('SELECT id, nombre, apellido, dni, email, rol FROM usuarios WHERE id = ? AND fecha_archivado IS NULL');
$s->execute([$id]);
$usuario = $s->fetch();
if (!$usuario) { flash('error', 'El usuario no existe o está archivado.'); header('Location: admin_usuarios.php'); exit; }

$errores = [];
$v = $usuario;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $v = datos_post();
    $v['rol'] = ($_POST['rol'] ?? '') === 'admin' ? 'admin' : 'usuario';

    // Un admin no puede quitarse a sí mismo el rol (evita quedarse sin administradores).
    if ($id === (int)$_SESSION['usuario_id']) $v['rol'] = 'admin';

    $errores = validar_usuario($pdo, $v, false, $id);

    if (!$errores) {
        try {
            if ($v['password'] !== '') {
                $s = $pdo->prepare('UPDATE usuarios SET nombre=?, apellido=?, dni=?, email=?, rol=?, password=? WHERE id=?');
                $s->execute([$v['nombre'], $v['apellido'], $v['dni'], $v['email'], $v['rol'],
                             password_hash($v['password'], PASSWORD_DEFAULT), $id]);
            } else {
                $s = $pdo->prepare('UPDATE usuarios SET nombre=?, apellido=?, dni=?, email=?, rol=? WHERE id=?');
                $s->execute([$v['nombre'], $v['apellido'], $v['dni'], $v['email'], $v['rol'], $id]);
            }
            if ($id === (int)$_SESSION['usuario_id']) $_SESSION['nombre'] = $v['nombre'];
            flash('ok', 'Datos actualizados correctamente.');
            header('Location: admin_usuarios.php');
            exit;
        } catch (PDOException $ex) {
            $errores[] = $ex->getCode() === '23000'
                ? 'El DNI o el correo ya pertenecen a otro usuario.'
                : 'No se pudieron guardar los cambios.';
        }
    }
}

$titulo = 'Editar usuario';
require 'includes/header.php';
?>
<section class="tarjeta tarjeta-form">
    <h1>Editar usuario</h1>
    <?php mostrar_errores($errores); ?>
    <form method="post" novalidate>
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= (int)$id ?>">
        <?php $conRol = true; $passObligatoria = false; $confirmar = false; require 'includes/campos_usuario.php'; ?>
        <p class="nota">Dejá la contraseña vacía para mantener la actual.</p>
        <div class="acciones">
            <button class="boton" type="submit">Guardar cambios</button>
            <a class="boton boton-claro" href="admin_usuarios.php">Cancelar</a>
        </div>
    </form>
</section>
<?php require 'includes/footer.php'; ?>
