# Tienda de entradas (PHP + MySQL)

## Instalación con XAMPP
1. Copiá esta carpeta en `C:\xampp\htdocs\entradas`.
2. Encendé **Apache** y **MySQL** en XAMPP.
3. En http://localhost/phpmyadmin → pestaña **SQL** → pegá todo el contenido de `sql/entradas_db.sql` → **Continuar**.
   (crea la base, las tablas, las 2 cuentas y los 6 productos; no hace falta instalar_cuentas.php)
4. Cuentas: administrador `admin@entraya.com` / `Admin1234` · comprador `usuario@entraya.com` / `Usuario1234`.
5. Las imágenes están en `img/productos/` (producto1.svg ... producto6.svg). Podés reemplazarlas por tus fotos
   desde Gestionar productos → Editar → Imagen.

## Páginas
- Públicas: index, productos (6 opciones), producto (detalle), carrito, carrito_accion.
- Cuenta: registro, login, panel, cerrar_sesion.
- Administrador: admin_productos, crear_producto, editar_producto, eliminar_producto,
  admin_usuarios, crear_usuario, editar_usuario, archivar_usuario, usuarios_archivados, reactivar_usuario.
