-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS sweetmett_db;
USE sweetmett_db;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
    telefono VARCHAR(20),
    direccion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255),
    stock INT NOT NULL DEFAULT 0,
    categoria_id INT,
    es_nuevo BOOLEAN DEFAULT FALSE,
    es_popular BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Tabla del carrito
CREATE TABLE IF NOT EXISTS carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Tabla de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'procesando', 'completado', 'cancelado') DEFAULT 'pendiente',
    total DECIMAL(10,2) NOT NULL,
    direccion_entrega TEXT,
    telefono_contacto VARCHAR(20),
    notas TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de detalles de pedido
CREATE TABLE IF NOT EXISTS detalles_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Administrador', 'admin@sweetmett.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- La contraseña por defecto es 'password'

-- Insertar algunas categorías por defecto
INSERT INTO categorias (nombre, descripcion) VALUES
('Chocolate Negro', 'Chocolates con alto contenido de cacao'),
('Chocolate con Leche', 'Chocolates suaves y cremosos'),
('Chocolate Blanco', 'Chocolates elaborados con manteca de cacao'),
('Trufas', 'Chocolates rellenos y trufas especiales'),
('Bombones', 'Bombones artesanales variados'),
('Chocolate con Frutos', 'Chocolates con frutas y frutos secos');

-- Insertar productos por defecto
INSERT INTO productos (nombre, descripcion, precio, imagen, stock, categoria_id, es_nuevo, es_popular) VALUES
('Chocolate con Leche', 'Delicioso chocolate artesanal con leche, suave y cremoso.', 2.50, 'assets/leche.webp', 100, 2, 0, 1),
('Chocolate Negro 70%', 'Intenso chocolate negro con 70% de cacao puro.', 3.00, 'assets/negro.webp', 100, 1, 0, 0),
('Chocolate Blanco', 'Suave y dulce chocolate blanco con vainilla natural.', 2.80, 'assets/blanco.webp', 100, 3, 0, 0),
('Chocolate con Frutos', 'Chocolate con leche mezclado con almendras y nueces.', 3.50, 'assets/frutos.webp', 100, 6, 1, 0),
('Trufas de Chocolate', 'Delicadas trufas de chocolate negro con cobertura de cacao.', 4.00, 'assets/galeria1.webp', 100, 4, 0, 0),
('Bombones Surtidos', 'Caja de bombones variados con diferentes rellenos.', 5.00, 'assets/galeria2.webp', 100, 5, 0, 1),
('Chocolate con Menta', 'Chocolate negro relleno de crema de menta refrescante.', 3.20, 'assets/galeria3.webp', 100, 1, 1, 0),
('Chocolate Especial', 'Chocolate premium con diseño especial y decoración artesanal.', 6.00, 'assets/poster.webp', 100, 1, 0, 0);

<?php
$sql = "SELECT p.*, u.nombre 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id";