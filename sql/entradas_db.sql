-- Base de datos del TP final. Pegar en phpMyAdmin > pestaña SQL > Continuar.
CREATE DATABASE IF NOT EXISTS entradas_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE entradas_db;

CREATE TABLE IF NOT EXISTS usuarios (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre          VARCHAR(60)  NOT NULL,
  apellido        VARCHAR(60)  NOT NULL,
  dni             VARCHAR(8)   NOT NULL,
  email           VARCHAR(120) NOT NULL,
  password        VARCHAR(255) NOT NULL,              -- hash de password_hash()
  rol             ENUM('usuario','admin') NOT NULL DEFAULT 'usuario',  -- 'usuario' = comprador
  fecha_creacion  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_archivado DATETIME NULL DEFAULT NULL,         -- NULL = cuenta activa
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_dni (dni),
  UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS productos (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre          VARCHAR(100) NOT NULL,
  descripcion     TEXT NOT NULL,
  precio          INT UNSIGNED NOT NULL,
  stock           INT UNSIGNED NOT NULL DEFAULT 0,
  imagen          VARCHAR(255) NULL DEFAULT NULL,     -- archivo dentro de img/productos/
  fecha_creacion  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cuentas: administrador (Admin1234) y comprador (Usuario1234).
-- Si ya existían, se les restablece la contraseña y el rol.
INSERT INTO usuarios (nombre, apellido, dni, email, password, rol) VALUES
('Admin',     'Sistema', '10000000', 'admin@entraya.com',   '$2y$10$4yNl/dn0rz7a9RsB/q6gDOKtiNPl3AtHuoHy0DKbek.WoT6Y4dYFe', 'admin'),
('Comprador', 'Prueba',  '20000000', 'usuario@entraya.com', '$2y$10$Di1I2WGyNp112EIPoyWH9eCzNCFQ32UwKvTv6pdQMVTRrBttBNYne',  'usuario')
ON DUPLICATE KEY UPDATE password = VALUES(password), rol = VALUES(rol), fecha_archivado = NULL;

-- 6 productos de ejemplo (reinicia la tabla de productos)
TRUNCATE TABLE productos;
INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES
('Entrada General - Recital de rock', 'Acceso general al recital. Incluye ingreso por puerta principal.', 35000, 100, 'producto1.svg'),
('Entrada VIP - Recital de rock', 'Ubicación preferencial, barra exclusiva e ingreso rápido.', 75000, 30, 'producto2.svg'),
('Entrada Platea - Obra de teatro', 'Butaca en platea baja, con muy buena visibilidad del escenario.', 28000, 60, 'producto3.svg'),
('Entrada Campo - Festival de verano', 'Acceso al campo durante los tres días del festival.', 42000, 80, 'producto4.svg'),
('Pack x4 Entradas - Partido', 'Cuatro entradas populares para el mismo partido. Ideal para ir en grupo.', 60000, 20, 'producto5.svg'),
('Entrada Palco - Ópera', 'Palco para disfrutar la función con la mejor vista de la sala.', 90000, 10, 'producto6.svg');
