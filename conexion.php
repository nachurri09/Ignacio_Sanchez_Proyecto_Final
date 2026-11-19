<?php
// Conexión a MySQL con PDO. Datos por defecto de XAMPP.
$host    = 'localhost';
$bd      = 'entradas_db';   // nombre de la base de datos
$usuario = 'root';
$clave   = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$bd;charset=utf8mb4", $usuario, $clave, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    exit('No se pudo conectar a la base de datos. Revisá que MySQL esté encendido en XAMPP y que hayas importado sql/entradas_db.sql.');
}
