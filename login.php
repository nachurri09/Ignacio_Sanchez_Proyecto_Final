<?php
require 'conexion.php';
require 'includes/funciones.php';

if (!empty($_SESSION['usuario_id'])) { header('Location: panel.php'); exit; }

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $clave = $_POST['password'] ?? '';

    if ($email === '' || $clave === '') {
        $error = 'Completá el correo y la contraseña.';
    } else {
        // Solo usuarios activos (no archivados)
        $s = $pdo->prepare('SELECT id, nombre, apellido, password, rol FROM usuarios
                            WHERE email = ? AND fecha_archivado IS NULL');
        $s->execute([$email]);
        $u = $s->fetch();

        if ($u && password_verify($clave, $u['password'])) {
            session_regenerate_id(true); // evita fijación de sesión
            $_SESSION['usuario_id'] = (int)$u['id'];
            $_SESSION['nombre']     = $u['nombre'];
            $_SESSION['rol']        = $u['rol'];
            header('Location: ' . ($u['rol'] === 'admin' ? 'admin_usuarios.php' : 'panel.php'));
            exit;
        }
        $error = 'Correo o contraseña incorrectos.';
    }
}

$titulo = 'Ingresar';
require 'includes/header.php';
?>
<section class="tarjeta tarjeta-form tarjeta-chica">
    <h1>Ingresar</h1>
    <?php if ($error) mostrar_errores([$error]); ?>
    <form method="post" novalidate>
        <?= csrf_campo() ?>
        <label>Correo electrónico
            <input type="email" name="email" required value="<?= e($email) ?>" autofocus>
        </label>
        <label>Contraseña
            <input type="password" name="password" required>
        </label>
        <button class="boton" type="submit">Entrar</button>
    </form>
    <p class="nota">¿No tenés cuenta? <a href="registro.php">Registrate</a></p>
</section>
<?php require 'includes/footer.php'; ?>
