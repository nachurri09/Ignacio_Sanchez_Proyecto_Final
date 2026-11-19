<?php
require 'conexion.php';
require 'includes/funciones.php';
requerir_admin();

$errores = [];
$v = ['rol' => 'usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $v = datos_post();
    $v['rol'] = ($_POST['rol'] ?? '') === 'admin' ? 'admin' : 'usuario'; // lista blanca
    $errores = validar_usuario($pdo, $v, true);

    if (!$errores) {
        try {
            $s = $pdo->prepare('INSERT INTO usuarios (nombre, apellido, dni, email, password, rol)
                                VALUES (?, ?, ?, ?, ?, ?)');
            $s->execute([$v['nombre'], $v['apellido'], $v['dni'], $v['email'],
                         password_hash($v['password'], PASSWORD_DEFAULT), $v['rol']]);
            flash('ok', 'Usuario registrado correctamente.');
            header('Location: admin_usuarios.php');
            exit;
        } catch (PDOException $ex) {
            $errores[] = $ex->getCode() === '23000'
                ? 'El DNI o el correo ya están registrados.'
                : 'No se pudo registrar el usuario.';
        }
    }
}

$titulo = 'Registrar usuario';
require 'includes/header.php';
?>
<section class="tarjeta tarjeta-form">
    <h1>Registrar usuario</h1>
    <?php mostrar_errores($errores); ?>
    <form method="post" novalidate>
        <?= csrf_campo() ?>
        <?php $conRol = true; $passObligatoria = true; $confirmar = false; require 'includes/campos_usuario.php'; ?>
        <div class="acciones">
            <button class="boton" type="submit">Guardar</button>
            <a class="boton boton-claro" href="admin_usuarios.php">Cancelar</a>
        </div>
    </form>
</section>
<?php require 'includes/footer.php'; ?>
