<?php
// Funciones compartidas: sesión, escape, CSRF, permisos y validación.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITIO', 'EntraYa'); // nombre de la página

// Escapa texto antes de mostrarlo en HTML (evita XSS).
function e($texto): string {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// ---- CSRF: token que prueba que el formulario salió de nuestro sitio ----
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_campo(): string {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
function csrf_validar(): void {
    $enviado = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $enviado)) {
        http_response_code(403);
        exit('Solicitud no válida (token de seguridad incorrecto). Volvé atrás y recargá la página.');
    }
}

// ---- Permisos ----
function requerir_login(): void {
    if (empty($_SESSION['usuario_id'])) {
        flash('error', 'Tenés que iniciar sesión para entrar ahí.');
        header('Location: login.php');
        exit;
    }
}
function requerir_admin(): void {
    requerir_login();
    if (($_SESSION['rol'] ?? '') !== 'admin') {
        flash('error', 'No tenés permisos de administrador.');
        header('Location: panel.php');
        exit;
    }
}

// ---- Mensajes que se muestran una sola vez ----
function flash(string $tipo, string $mensaje): void {
    $_SESSION['flash'] = [$tipo, $mensaje];
}
function mostrar_flash(): void {
    if (!empty($_SESSION['flash'])) {
        [$tipo, $mensaje] = $_SESSION['flash'];
        echo '<div class="aviso aviso-' . e($tipo) . '">' . e($mensaje) . '</div>';
        unset($_SESSION['flash']);
    }
}

// ---- Datos del formulario de usuario ----
function datos_post(): array {
    $d = [
        'nombre'   => trim($_POST['nombre']   ?? ''),
        'apellido' => trim($_POST['apellido'] ?? ''),
        'dni'      => trim($_POST['dni']      ?? ''),
        'email'    => strtolower(trim($_POST['email'] ?? '')),
        'password' => $_POST['password'] ?? '',
    ];
    if (isset($_POST['password2'])) {
        $d['password2'] = $_POST['password2'];
    }
    return $d;
}

// Devuelve una lista de errores (vacía si todo está bien).
function validar_usuario(PDO $pdo, array $d, bool $exigirPass, ?int $idActual = null): array {
    $err = [];
    if ($d['nombre'] === '' || mb_strlen($d['nombre']) > 60)     $err[] = 'El nombre es obligatorio (máximo 60 caracteres).';
    if ($d['apellido'] === '' || mb_strlen($d['apellido']) > 60) $err[] = 'El apellido es obligatorio (máximo 60 caracteres).';
    if (!preg_match('/^\d{7,8}$/', $d['dni']))                    $err[] = 'El DNI debe tener 7 u 8 números, sin puntos.';
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['email']) > 120) $err[] = 'Ingresá un correo electrónico válido.';

    if ($exigirPass || $d['password'] !== '') {
        if (strlen($d['password']) < 8) $err[] = 'La contraseña debe tener al menos 8 caracteres.';
        if (isset($d['password2']) && $d['password'] !== $d['password2']) $err[] = 'Las contraseñas no coinciden.';
    }

    if (!$err) { // duplicados (se ignora el propio usuario al editar)
        $id = $idActual ?? 0;
        $s = $pdo->prepare('SELECT 1 FROM usuarios WHERE dni = ? AND id <> ?');
        $s->execute([$d['dni'], $id]);
        if ($s->fetch()) $err[] = 'Ya existe un usuario con ese DNI.';

        $s = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = ? AND id <> ?');
        $s->execute([$d['email'], $id]);
        if ($s->fetch()) $err[] = 'Ya existe un usuario con ese correo electrónico.';
    }
    return $err;
}

function mostrar_errores(array $errores): void {
    if ($errores) {
        echo '<div class="aviso aviso-error"><ul>';
        foreach ($errores as $x) echo '<li>' . e($x) . '</li>';
        echo '</ul></div>';
    }
}

function precio($n): string {
    return '$ ' . number_format((float)$n, 0, ',', '.');
}

// ---- Productos ----
function datos_producto(): array {
    return [
        'nombre'      => trim($_POST['nombre'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'precio'      => trim($_POST['precio'] ?? ''),
        'stock'       => trim($_POST['stock'] ?? ''),
    ];
}

function validar_producto(array $d): array {
    $err = [];
    if ($d['nombre'] === '' || mb_strlen($d['nombre']) > 100) $err[] = 'El nombre es obligatorio (máximo 100 caracteres).';
    if (mb_strlen($d['descripcion']) > 1000)                  $err[] = 'La descripción no puede superar los 1000 caracteres.';
    if (!ctype_digit($d['precio']) || (int)$d['precio'] < 1 || (int)$d['precio'] > 9999999) $err[] = 'El precio debe ser un número entero mayor a 0.';
    if (!ctype_digit($d['stock'])  || (int)$d['stock'] > 9999) $err[] = 'El stock debe ser un número entre 0 y 9999.';
    return $err;
}

// Sube la imagen a img/productos/ y devuelve el nombre del archivo (o null si no se subió nada).
function subir_imagen(array $f, array &$err): ?string {
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] !== UPLOAD_ERR_OK)  { $err[] = 'No se pudo subir la imagen.'; return null; }
    if ($f['size'] > 2 * 1024 * 1024)   { $err[] = 'La imagen no puede pesar más de 2 MB.'; return null; }

    $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']); // se mira el contenido real, no el nombre
    if (!isset($permitidos[$mime])) { $err[] = 'Solo se permiten imágenes JPG, PNG o WEBP.'; return null; }

    $nombre = bin2hex(random_bytes(8)) . '.' . $permitidos[$mime];
    $dir = __DIR__ . '/../img/productos';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($f['tmp_name'], "$dir/$nombre")) { $err[] = 'No se pudo guardar la imagen.'; return null; }
    return $nombre;
}

// Muestra la imagen del producto, o un cuadro gris si todavía no hay imagen.
function img_producto(?string $archivo, string $alt, string $clase = 'img-producto'): string {
    $archivo = $archivo ? basename($archivo) : '';
    if ($archivo !== '' && is_file(__DIR__ . '/../img/productos/' . $archivo)) {
        return '<img class="' . e($clase) . '" src="img/productos/' . e($archivo) . '" alt="' . e($alt) . '">';
    }
    return '<div class="' . e($clase) . ' sin-imagen">Sin imagen</div>';
}

// Solo borra imágenes subidas desde el panel (nombre aleatorio), nunca las que pusiste a mano.
function borrar_imagen(?string $archivo): void {
    if ($archivo && preg_match('/^[0-9a-f]{16}\.(jpg|png|webp)$/', $archivo)) {
        $ruta = __DIR__ . '/../img/productos/' . $archivo;
        if (is_file($ruta)) unlink($ruta);
    }
}

// ---- Carrito (se guarda en la sesión: [id_producto => cantidad]) ----
function cantidad_carrito(): int {
    return (int)array_sum($_SESSION['carrito'] ?? []);
}
