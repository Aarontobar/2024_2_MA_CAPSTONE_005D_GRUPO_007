SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

USE restaurante_bd;

-- Eliminar las tablas existentes si existen
DROP TABLE IF EXISTS historial_pedidos;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS promociones;
DROP TABLE IF EXISTS Mesa;
DROP TABLE IF EXISTS Reserva;
DROP TABLE IF EXISTS Ingredientes;
DROP TABLE IF EXISTS Platillos;
DROP TABLE IF EXISTS Platillo_Ingrediente;
DROP TABLE IF EXISTS Pedido;
DROP TABLE IF EXISTS Detalle_Pedido_Platillo;

-- Crear la tabla `usuarios`
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    rut VARCHAR(20) NOT NULL UNIQUE,
    horario TIME NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    telefono VARCHAR(15) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    direccion VARCHAR(255) DEFAULT NULL,
    fecha_ingreso DATE NOT NULL,
    tipo_usuario ENUM('administrador', 'cocina', 'mesero', 'metre') NOT NULL DEFAULT 'mesero',
    PRIMARY KEY (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Crear la tabla `promociones`
CREATE TABLE promociones (
    id_promocion INT NOT NULL AUTO_INCREMENT,
    nombre_promocion VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    descuento DECIMAL(5, 2) NOT NULL,
    estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
    condiciones JSON NOT NULL,
    accion JSON NOT NULL,
    ruta_foto VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id_promocion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Crear la tabla `Mesa`
CREATE TABLE Mesa (
    id_mesa INT NOT NULL AUTO_INCREMENT,
    cantidad_asientos INT NOT NULL,
    estado ENUM('Disponible', 'Ocupada', 'Reservada', 'En Espera', 'Para Limpiar') NOT NULL DEFAULT 'Disponible',
    PRIMARY KEY (id_mesa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE detalle_mesero_mesa (
    id_detalle INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_mesa INT NOT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    PRIMARY KEY (id_detalle),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_mesa) REFERENCES Mesa(id_mesa) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Crear la tabla `Reserva`
CREATE TABLE Reserva (
    id_reserva INT NOT NULL AUTO_INCREMENT,
    nombre_reserva VARCHAR(255) NOT NULL,
    apellido_reserva VARCHAR(255) NOT NULL,
    cantidad_personas INT NOT NULL,
    hora TIME NOT NULL,
    fecha DATE NOT NULL,
    id_mesa INT NOT NULL,
    estado_reserva ENUM('Pendiente', 'Realizada', 'Cancelada', 'Completada') NOT NULL DEFAULT 'Pendiente',
    PRIMARY KEY (id_reserva),
    FOREIGN KEY (id_mesa) REFERENCES Mesa(id_mesa) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Crear la tabla `Ingredientes`
CREATE TABLE Ingredientes (
    id_ingrediente INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    cantidad DECIMAL(10, 2) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    unidad_medida VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_ingrediente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Crear la tabla `Platillos`
CREATE TABLE Platillos (
    id_platillo INT NOT NULL AUTO_INCREMENT,
    nombre_platillo VARCHAR(255) NOT NULL,
    descripcion_platillo TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    estado ENUM('Disponible', 'No Disponible') NOT NULL DEFAULT 'Disponible',
    tiempo_preparacion TIME NOT NULL,
    ruta_foto VARCHAR(255),
    tipo_platillo ENUM('Entrada', 'Plato Principal', 'Acompañamientos', 'Postres', 'Menú Infantil', 'Bebida') NOT NULL DEFAULT 'Plato Principal',
    PRIMARY KEY (id_platillo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Crear la tabla `Platillo_Ingrediente`
CREATE TABLE Platillo_Ingrediente (
    id_platillo INT NOT NULL,
    id_ingrediente INT NOT NULL,
    cantidad_utilizada DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (id_platillo, id_ingrediente),
    FOREIGN KEY (id_platillo) REFERENCES Platillos(id_platillo) ON DELETE CASCADE,
    FOREIGN KEY (id_ingrediente) REFERENCES Ingredientes(id_ingrediente) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Crear la tabla `Pedido`
CREATE TABLE Pedido (
    id_pedido INT NOT NULL AUTO_INCREMENT,
    id_detalle_mesero_mesa INT NULL, -- Referencia a la tabla detalle_mesero_mesa
    total_cuenta DECIMAL(10, 2) NOT NULL,
    hora TIME NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('recibido', 'en preparación', 'preparado', 'servido', 'completado', 'cancelado') NOT NULL DEFAULT 'recibido',
    tipo ENUM('Delivery', 'Para Llevar', 'Para Servir') NOT NULL DEFAULT 'Para Servir',
    prioridad ENUM('prioritario', 'normal') NOT NULL DEFAULT 'normal', -- Nuevo campo agregado
    estado_pago ENUM('pagado', 'pendiente') NOT NULL DEFAULT 'pendiente', -- Nuevo campo para estado del pago
    PRIMARY KEY (id_pedido),
    FOREIGN KEY (id_detalle_mesero_mesa) REFERENCES detalle_mesero_mesa(id_detalle) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Crear la tabla `Detalle_Pedido_Platillo`
CREATE TABLE Detalle_Pedido_Platillo (
    id_pedido INT NOT NULL,
    id_platillo INT NOT NULL,
    cantidad INT NOT NULL,
    PRIMARY KEY (id_pedido, id_platillo),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_platillo) REFERENCES Platillos(id_platillo) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE Estado_Dia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    estado ENUM('Iniciado', 'No Iniciado') NOT NULL DEFAULT 'No Iniciado',
    mesas_disponibles TEXT, -- ID de mesas disponibles separados por coma
    platillos_no_disponibles TEXT, -- ID de platillos no disponibles separados por coma
    hora_cierre TIME, -- Campo para la hora de cierre
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear la tabla `mensajes`
CREATE TABLE mensajes (
    id_mensaje INT NOT NULL AUTO_INCREMENT,
    id_usuario_envia INT NOT NULL,
    id_usuario_recibe INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_mensaje),
    FOREIGN KEY (id_usuario_envia) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario_recibe) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Crear la tabla `Reseñas`
CREATE TABLE Reseñas (
    id_reseña INT NOT NULL AUTO_INCREMENT,
    id_pedido INT NOT NULL, -- Referencia al pedido que se está evaluando
    nombre_cliente VARCHAR(100) NOT NULL, -- Nombre del cliente que deja la reseña
    apellido_cliente VARCHAR(100) NOT NULL, -- Apellido del cliente
    calificacion DECIMAL(2, 1) NOT NULL CHECK (calificacion BETWEEN 1.0 AND 5.0), -- Calificación con decimales entre 1.0 y 5.0
    comentario TEXT, -- Comentario opcional del cliente
    fecha_reseña DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Fecha en la que se deja la reseña
    PRIMARY KEY (id_reseña),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido) ON DELETE CASCADE -- Si se elimina el pedido, también se elimina la reseña
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

COMMIT;

-- Insertar usuarios (1 de cada tipo, y el resto meseros)
INSERT INTO usuarios (nombre_usuario, contrasena, nombre, rut, horario, disponible, telefono, email, direccion, fecha_ingreso, tipo_usuario) VALUES
('admin', 'contraseña_admin', 'Administrador Principal', '12345678-9', '09:00:00', TRUE, '123456789', 'admin@example.com', 'Calle Principal 123', '2024-01-01', 'administrador'),
('chef1', 'contraseña_chef', 'Chef Juan', '98765432-1', '08:00:00', TRUE, '987654321', 'chef1@example.com', 'Avenida Gourmet 456', '2024-01-01', 'cocina'),
('metre1', 'contraseña_met', 'Metre Ana', '32165498-7', '09:30:00', TRUE, '321654987', 'metre1@example.com', 'Calle del Vino 321', '2024-01-01', 'metre'),
('mesero1', 'contraseña_mesero1', 'Mesero Pablo', '45678901-2', '10:00:00', TRUE, '456789123', 'mesero1@example.com', 'Calle del Sabor 789', '2024-01-01', 'mesero'),
('mesero2', 'contraseña_mesero2', 'Mesero Juan', '12378945-6', '11:00:00', TRUE, '123789456', 'mesero2@example.com', 'Calle del Gusto 456', '2024-01-02', 'mesero'),
('mesero3', 'contraseña_mesero3', 'Mesero Ana', '11223344-5', '10:30:00', TRUE, '112233445', 'mesero3@example.com', 'Calle La Paz 890', '2024-01-03', 'mesero'),
('mesero4', 'contraseña_mesero4', 'Mesero Luis', '55667788-9', '09:45:00', TRUE, '556677889', 'mesero4@example.com', 'Avenida Central 456', '2024-01-04', 'mesero'),
('mesero5', 'contraseña_mesero5', 'Mesero Carlos', '99887766-5', '12:00:00', TRUE, '998877665', 'mesero5@example.com', 'Calle Victoria 123', '2024-01-05', 'mesero'),
('mesero6', 'contraseña_mesero6', 'Mesero Sofía', '33445566-7', '11:15:00', TRUE, '334455667', 'mesero6@example.com', 'Avenida Paz 789', '2024-01-06', 'mesero'),
('mesero7', 'contraseña_mesero7', 'Mesero Pedro', '77889900-1', '12:30:00', TRUE, '778899001', 'mesero7@example.com', 'Calle Rápida 456', '2024-01-07', 'mesero'),
('mesero8', 'contraseña_mesero8', 'Mesero Isabel', '22334455-6', '10:45:00', TRUE, '223344556', 'mesero8@example.com', 'Calle Roma 321', '2024-01-08', 'mesero'),
('mesero9', 'contraseña_mesero9', 'Mesero Camila', '12344321-1', '09:50:00', TRUE, '123443211', 'mesero9@example.com', 'Avenida Sol 789', '2024-01-09', 'mesero'),
('mesero10', 'contraseña_mesero10', 'Mesero Mateo', '98765432-0', '11:35:00', TRUE, '987654320', 'mesero10@example.com', 'Calle Nube 456', '2024-01-10', 'mesero');

-- Insertar mesas
INSERT INTO Mesa (cantidad_asientos, estado) VALUES
(4, 'Disponible'),
(2, 'Ocupada'),
(6, 'Reservada'),
(4, 'Disponible'),
(2, 'Para Limpiar'),
(4, 'Disponible'),
(6, 'Ocupada'),
(4, 'En Espera'),
(8, 'Reservada'),
(4, 'Disponible'),
(2, 'Disponible'),
(6, 'Disponible'),
(4, 'Ocupada'),
(4, 'Para Limpiar'),
(4, 'Reservada'),
(8, 'Ocupada'),
(6, 'En Espera'),
(4, 'Disponible'),
(2, 'Ocupada'),
(8, 'Reservada');



-- Insertar reservas
INSERT INTO Reserva (nombre_reserva, apellido_reserva, cantidad_personas, hora, fecha, id_mesa, estado_reserva) VALUES
('Ana', 'Gómez', 4, '19:00:00', '2024-09-15', 1, 'Pendiente'),
('Luis', 'Martínez', 2, '20:00:00', '2024-09-15', 2, 'Realizada'),
('Pedro', 'Hernández', 6, '21:00:00', '2024-09-15', 3, 'Completada'),
('Laura', 'Rodríguez', 4, '18:00:00', '2024-09-15', 4, 'Cancelada'),
('Carlos', 'Fernández', 2, '22:00:00', '2024-09-15', 5, 'Pendiente'),
('Sofía', 'López', 4, '19:30:00', '2024-09-16', 6, 'Realizada'),
('Andrés', 'Pérez', 5, '20:30:00', '2024-09-16', 7, 'Pendiente'),
('Lucía', 'Mendoza', 3, '21:30:00', '2024-09-16', 8, 'Cancelada'),
('Fernando', 'Santos', 2, '18:30:00', '2024-09-16', 9, 'Completada'),
('Alejandro', 'Ortiz', 6, '20:00:00', '2024-09-16', 10, 'Realizada'),
('Daniela', 'Ríos', 4, '19:45:00', '2024-09-17', 11, 'Pendiente'),
('Julio', 'Castro', 5, '20:15:00', '2024-09-17', 12, 'Completada'),
('Verónica', 'Ramírez', 2, '21:15:00', '2024-09-17', 13, 'Cancelada'),
('Esteban', 'Navarro', 4, '18:45:00', '2024-09-17', 14, 'Realizada'),
('Gloria', 'Luna', 3, '19:15:00', '2024-09-17', 15, 'Pendiente'),
('Martín', 'Campos', 5, '21:00:00', '2024-09-17', 16, 'Pendiente'),
('Paula', 'Vargas', 2, '18:00:00', '2024-09-18', 17, 'Cancelada'),
('Pablo', 'Cruz', 6, '19:30:00', '2024-09-18', 18, 'Realizada'),
('Clara', 'Rojas', 4, '20:00:00', '2024-09-18', 19, 'Completada'),
('Felipe', 'Suárez', 3, '19:00:00', '2024-09-18', 20, 'Pendiente');

-- Insertar ingredientes
INSERT INTO Ingredientes (nombre, cantidad, precio, unidad_medida) VALUES
('Tomate', 50.00, 2.00, 'kg'),
('Lechuga', 30.00, 1.50, 'kg'),
('Carne de Res', 100.00, 10.00, 'kg'),
('Queso', 20.00, 5.00, 'kg'),
('Pollo', 70.00, 7.00, 'kg'),
('Cebolla', 40.00, 2.50, 'kg'),
('Pimientos', 35.00, 3.00, 'kg'),
('Papas', 60.00, 1.20, 'kg'),
('Champiñones', 25.00, 4.00, 'kg'),
('Zanahorias', 45.00, 1.80, 'kg'),
('Aguacate', 25.00, 3.00, 'kg'),
('Cilantro', 15.00, 1.00, 'kg'),
('Ajo', 20.00, 1.50, 'kg'),
('Aceitunas', 10.00, 2.50, 'kg'),
('Pepino', 20.00, 1.20, 'kg'),
('Calabacín', 35.00, 2.00, 'kg'),
('Harina', 100.00, 0.80, 'kg'),
('Azúcar', 50.00, 0.70, 'kg'),
('Sal', 60.00, 0.50, 'kg'),
('Aceite de Oliva', 40.00, 8.00, 'lt');

-- Insertar platillos
INSERT INTO Platillos (nombre_platillo, descripcion_platillo, precio, estado, tiempo_preparacion, ruta_foto, tipo_platillo) VALUES
('Ensalada César', 'Ensalada con pollo, queso parmesano y aderezo César.', 12.00, 'Disponible', '00:15:00', 'ensalada_cesar.jpg', 'Entrada'),
('Pizza Margarita', 'Pizza con tomate, mozzarella y albahaca.', 15.00, 'Disponible', '00:30:00', 'pizza_margarita.jpg', 'Plato Principal'),
('Pasta Alfredo', 'Pasta con salsa Alfredo y pollo.', 14.00, 'Disponible', '00:25:00', 'pasta_alfredo.jpg', 'Plato Principal'),
('Brownie', 'Brownie de chocolate con nueces.', 6.00, 'Disponible', '00:10:00', 'brownie.jpg', 'Postres'),
('Limonada', 'Limonada fresca y natural.', 5.00, 'Disponible', '00:05:00', 'limonada.jpg', 'Bebida'),
('Sopa de Tomate', 'Sopa de tomate fresca con albahaca.', 7.00, 'Disponible', '00:20:00', 'sopa_tomate.jpg', 'Entrada'),
('Hamburguesa', 'Hamburguesa de res con queso, tomate y lechuga.', 10.00, 'Disponible', '00:15:00', 'hamburguesa.jpg', 'Plato Principal'),
('Tarta de Manzana', 'Tarta casera de manzana con helado de vainilla.', 8.00, 'Disponible', '00:15:00', 'tarta_manzana.jpg', 'Postres'),
('Soda', 'Soda de diferentes sabores.', 3.00, 'Disponible', '00:05:00', 'soda.jpg', 'Bebida'),
('Panini', 'Panini con jamón y queso derretido.', 9.00, 'Disponible', '00:10:00', 'panini.jpg', 'Plato Principal'),
('Espagueti Carbonara', 'Espagueti con salsa carbonara y tocino.', 13.00, 'Disponible', '00:20:00', 'espagueti_carbonara.jpg', 'Plato Principal'),
('Té Helado', 'Té helado con limón.', 4.00, 'Disponible', '00:05:00', 'te_helado.jpg', 'Bebida'),
('Nachos con Queso', 'Nachos crujientes con queso derretido.', 7.00, 'Disponible', '00:10:00', 'nachos.jpg', 'Entrada'),
('Pastel de Chocolate', 'Pastel de chocolate con cobertura de chocolate.', 9.00, 'Disponible', '00:15:00', 'pastel_chocolate.jpg', 'Postres'),
('Agua Mineral', 'Agua mineral sin gas.', 2.00, 'Disponible', '00:03:00', 'agua_mineral.jpg', 'Bebida'),
('Pizza Pepperoni', 'Pizza con salsa de tomate, mozzarella y pepperoni.', 16.00, 'Disponible', '00:25:00', 'pizza_pepperoni.jpg', 'Plato Principal'),
('Sándwich Club', 'Sándwich club con pavo, jamón y queso.', 11.00, 'Disponible', '00:15:00', 'sandwich_club.jpg', 'Plato Principal'),
('Ensalada Mixta', 'Ensalada con lechuga, tomate, pepino y aguacate.', 10.00, 'Disponible', '00:12:00', 'ensalada_mixta.jpg', 'Entrada'),
('Pizza Vegetariana', 'Pizza con vegetales frescos y mozzarella.', 15.00, 'Disponible', '00:30:00', 'pizza_vegetariana.jpg', 'Plato Principal'),
('Helado de Fresa', 'Helado cremoso de fresa.', 5.00, 'Disponible', '00:05:00', 'helado_fresa.jpg', 'Postres');

-- Insertar platillos e ingredientes
INSERT INTO Platillo_Ingrediente (id_platillo, id_ingrediente, cantidad_utilizada) VALUES
(1, 1, 0.20),
(1, 2, 0.10),
(2, 1, 0.30),
(2, 3, 0.50),
(3, 3, 0.70),
(4, 4, 0.10),
(5, 5, 0.50),
(6, 6, 0.40),
(7, 7, 0.25),
(8, 8, 0.30),
(9, 9, 0.10),
(10, 10, 0.20),
(11, 11, 0.50),
(12, 12, 0.30),
(13, 13, 0.15),
(14, 14, 0.20),
(15, 15, 0.25),
(16, 16, 0.35),
(17, 17, 0.40),
(18, 18, 0.20);

-- Insertar promociones
INSERT INTO promociones (nombre_promocion, descripcion, descuento, estado, condiciones, accion, ruta_foto) VALUES
('Descuento Verano', '20% en platos principales', 20.00, 'Activo', '{"inicio": "2024-06-01", "fin": "2024-09-01"}', '{"tipo": "descuento", "valor": 20}', 'promocion_verano.jpg'),
('Happy Hour Bebidas', '20% de descuento en bebidas de 5pm a 7pm', 20.00, 'Activo', '{"inicio": "17:00", "fin": "19:00"}', '{"tipo": "descuento", "valor": 20}', 'happy_hour.jpg'),
('Combo Familiar', '10% para grupos de 4 o más', 10.00, 'Activo', '{"minimo_personas": 4}', '{"tipo": "descuento", "valor": 10}', 'combo_familiar.jpg'),
('Menú Infantil Gratis', 'Un menú gratis por dos platos principales', 100.00, 'Activo', '{"minimo_platos": 2}', '{"tipo": "gratis", "platillo": "Menú Infantil"}', 'menu_infantil.jpg'),
('Promoción de Lunes', '5% en todos los productos los lunes', 5.00, 'Activo', '{"dia": "Lunes"}', '{"tipo": "descuento", "valor": 5}', 'promocion_lunes.jpg'),
('Cena Romántica', '15% de descuento en cenas para dos personas', 15.00, 'Activo', '{"minimo_personas": 2}', '{"tipo": "descuento", "valor": 15}', 'cena_romantica.jpg'),
('Descuento para Estudiantes', '10% de descuento para estudiantes', 10.00, 'Activo', '{"condicion": "Estudiante"}', '{"tipo": "descuento", "valor": 10}', 'descuento_estudiantes.jpg'),
('Happy Hour Snacks', '2x1 en snacks de 4pm a 6pm', 50.00, 'Activo', '{"inicio": "16:00", "fin": "18:00"}', '{"tipo": "descuento", "valor": 50}', 'happy_hour_snacks.jpg'),
('Descuento Aniversario', '30% de descuento en todo el menú', 30.00, 'Activo', '{"fecha": "2024-12-01"}', '{"tipo": "descuento", "valor": 30}', 'descuento_aniversario.jpg'),
('Promoción Fin de Semana', '20% en platillos principales sábado y domingo', 20.00, 'Activo', '{"dia": ["Sábado", "Domingo"]}', '{"tipo": "descuento", "valor": 20}', 'promocion_fin_semana.jpg');

-- Insertar pedidos
INSERT INTO Pedido (total_cuenta, hora, fecha, estado, tipo) VALUES
(50000.00, '12:00:00', '2024-09-01', 'recibido', 'Para Servir'),
(30000.00, '12:30:00', '2024-09-01', 'recibido', 'Delivery'),
(25000.00, '13:00:00', '2024-09-01', 'recibido', 'Para Llevar'),
(45000.00, '13:15:00', '2024-09-01', 'recibido', 'Para Servir'),
(60000.00, '13:30:00', '2024-09-01', 'recibido', 'Delivery'),
(35000.00, '14:00:00', '2024-09-01', 'recibido', 'Para Llevar'),
(20000.00, '14:15:00', '2024-09-01', 'recibido', 'Para Servir'),
(55000.00, '14:30:00', '2024-09-01', 'recibido', 'Delivery'),
(70000.00, '15:00:00', '2024-09-01', 'recibido', 'Para Llevar'),
(80000.00, '15:30:00', '2024-09-01', 'recibido', 'Para Servir');

-- Insertar detalles de pedido y platillo
INSERT INTO Detalle_Pedido_Platillo (id_pedido, id_platillo, cantidad) VALUES
(1, 1, 2),
(1, 3, 1),
(1, 5, 3),
(2, 2, 1),
(2, 4, 2),
(2, 6, 1),
(3, 3, 2),
(3, 7, 1),
(3, 8, 2),
(4, 9, 1),
(4, 10, 2),
(4, 11, 1),
(5, 12, 2),
(5, 13, 1),
(5, 14, 2),
(6, 15, 1),
(6, 16, 2),
(6, 17, 1),
(7, 18, 2),
(7, 1, 1);