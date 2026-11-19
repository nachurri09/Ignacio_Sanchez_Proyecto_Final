<?php
// Crea el administrador, un comprador de prueba y 6 productos de ejemplo.
// Se puede abrir más de una vez: no duplica nada. IMPORTANTE: borrá este archivo al terminar.
require 'conexion.php';
require 'includes/funciones.php';

$cuentas = [
    ['Admin',    'Sistema', '10000000', 'admin@entraya.com',   'Admin1234',   'admin'],
    ['Comprador', 'Prueba', '20000000', 'usuario@entraya.com', 'Usuario1234', 'usuario'],
];

$salida = [];
foreach ($cuentas as [$nombre, $apellido, $dni, $email, $clave, $rol]) {
    $s = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = ? OR dni = ?');
    $s->execute([$email, $dni]);
    if ($s->fetch()) { $salida[] = "$email ya existe."; continue; }

    $s = $pdo->prepare('INSERT INTO usuarios (nombre, apellido, dni, email, password, rol) VALUES (?,?,?,?,?,?)');
    $s->execute([$nombre, $apellido, $dni, $email, password_hash($clave, PASSWORD_DEFAULT), $rol]);
    $salida[] = "Creado $rol: $email / $clave";
}

// Productos de ejemplo (solo si todavía no hay ninguno). Las imágenes las ponés vos en img/productos/
if ((int)$pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn() === 0) {
    $productos = [
        ['Entrada General - Recital de rock',  'Acceso general al recital. Incluye ingreso por puerta principal.',            35000, 100, 'producto1.svg'],
        ['Entrada VIP - Recital de rock',      'Ubicación preferencial, barra exclusiva e ingreso rápido.',                    75000,  30, 'producto2.svg'],
        ['Entrada Platea - Obra de teatro',    'Butaca en platea baja, con muy buena visibilidad del escenario.',              28000,  60, 'producto3.svg'],
        ['Entrada Campo - Festival de verano', 'Acceso al campo durante los tres días del festival.',                          42000,  80, 'producto4.svg'],
        ['Pack x4 Entradas - Partido',         'Cuatro entradas populares para el mismo partido. Ideal para ir en grupo.',     60000,  20, 'producto5.svg'],
        ['Entrada Palco - Ópera',              'Palco para disfrutar la función con la mejor vista de la sala.',               90000,  10, 'producto6.svg'],
    ];
    $ins = $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?,?,?,?,?)');
    foreach ($productos as $p) $ins->execute($p);
    $salida[] = 'Se cargaron 6 productos de ejemplo (imágenes esperadas: img/productos/producto1.svg ... producto6.svg).';
}

header('Content-Type: text/plain; charset=utf-8');
echo implode("\n", $salida) . "\n\nAhora borrá instalar_cuentas.php de la carpeta del proyecto.";
