<?php
require 'conexion.php';
require 'includes/funciones.php';

$errores = [];
$v = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $v = datos_post();
    $errores = validar_usuario($pdo, $v, true);

    if (!$errores) {
        try {
            $s = $pdo->prepare('INSERT INTO usuarios (nombre, apellido, dni, email, password, rol)
                                VALUES (?, ?, ?, ?, ?, "usuario")');
            $s->execute([$v['nombre'], $v['apellido'], $v['dni'], $v['email'],
                         password_hash($v['password'], PASSWORD_DEFAULT)]);
            flash('ok', 'Cuenta creada. Ya podés iniciar sesión.');
            header('Location: login.php');
            exit;
        } catch (PDOException $ex) {
            // 23000 = violación de UNIQUE (por si dos personas se registran a la vez)
            $errores[] = $ex->getCode() === '23000'
                ? 'El DNI o el correo ya están registrados.'
                : 'No se pudo crear la cuenta. Intentá de nuevo.';
        }
    }
}

$titulo = 'Crear cuenta';
require 'includes/header.php';
?>
<section class="tarjeta tarjeta-form">
    <h1>Crear cuenta</h1>
    <?php mostrar_errores($errores); ?>
    <form method="post" novalidate>
        <?= csrf_campo() ?>
        <?php $conRol = false; $passObligatoria = true; $confirmar = true; require 'includes/campos_usuario.php'; ?>
        <button class="boton" type="submit">Registrarme</button>
    </form>
    <p class="nota">¿Ya tenés cuenta? <a href="login.php">Ingresá</a></p>
</section>
<?php require 'includes/footer.php'; ?>
