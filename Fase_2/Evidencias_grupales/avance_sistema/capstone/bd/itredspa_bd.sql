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
    id_mesa INT NULL,  -- Cambiar a NULL para permitir que no se requiera un id_mesa
    id_usuario INT NULL,
    total_cuenta DECIMAL(10, 2) NOT NULL,
    hora TIME NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('En Cocina', 'En Preparación', 'Listo', 'Servido', 'Pagado') NOT NULL DEFAULT 'En Cocina',
    tipo ENUM('Delivery', 'Para Llevar', 'Para Servir') NOT NULL DEFAULT 'Para Servir',
    PRIMARY KEY (id_pedido),
    FOREIGN KEY (id_mesa) REFERENCES Mesa(id_mesa) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
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

-- Crear la tabla `historial_pedidos`
CREATE TABLE historial_pedidos (
    id_historial INT NOT NULL AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    id_mesa INT NOT NULL,
    id_usuario INT NOT NULL,
    total_cuenta DECIMAL(10, 2) NOT NULL,
    hora TIME NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('En Cocina', 'En Preparación', 'Listo', 'Servido', 'Pagado') NOT NULL DEFAULT 'En Cocina',
    tipo ENUM('Delivery', 'Para Llevar', 'Para Servir') NOT NULL DEFAULT 'Para Servir',
    PRIMARY KEY (id_historial),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_mesa) REFERENCES Mesa(id_mesa) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
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

COMMIT;

-- Inserts de ejemplo para la tabla usuarios
INSERT INTO usuarios (nombre_usuario, contrasena, nombre, rut, horario, disponible, telefono, email, direccion, fecha_ingreso, tipo_usuario) VALUES
('admin', 'contraseña_admin', 'Administrador Principal', '12345678-9', '09:00:00', TRUE, '123456789', 'admin@example.com', 'Calle Principal 123', '2024-01-01', 'administrador'),
('chef1', 'contraseña_chef', 'Chef Juan', '98765432-1', '08:00:00', TRUE, '987654321', 'chef1@example.com', 'Avenida Gourmet 456', '2024-01-01', 'cocina'),
('waiter1', 'contraseña_waiter', 'Mesero Pablo', '45678901-2', '10:00:00', TRUE, '456789123', 'waiter1@example.com', 'Calle del Sabor 789', '2024-01-01', 'mesero'),
('metre1', 'contraseña_met', 'Metre Ana', '32165498-7', '09:30:00', TRUE, '321654987', 'metre1@example.com', 'Calle del Vino 321', '2024-01-01', 'metre'),
('chef2', 'contraseña_chef2', 'Chef Maria', '85274196-3', '07:00:00', TRUE, '852741963', 'chef2@example.com', 'Calle de la Cocina 654', '2024-01-01', 'cocina');


-- Ejemplo de inserciones en la tabla promociones con condiciones, acciones y foto
INSERT INTO promociones (nombre_promocion, descripcion, descuento, estado, condiciones, accion, ruta_foto) VALUES
('Descuento de Verano', 
 'Descuento del 20% en todos los platos principales durante el verano.', 
 20.00, 
 'Activo', 
 '{"tipo": "fecha", "inicio": "2024-06-01", "fin": "2024-09-01", "tipo_platillo": "Plato Principal"}', 
 '{"tipo": "descuento_platillos", "valor": 20, "tipo_platillo": "Plato Principal"}',
 'imagenes/promociones/verano_2024.jpg'
),

('Happy Hour Bebidas', 
 '20% de descuento en todas las bebidas durante el happy hour (5pm a 7pm).', 
 20.00, 
 'Activo', 
 '{"tipo": "hora", "inicio": "17:00", "fin": "19:00", "tipo_platillo": "Bebida"}', 
 '{"tipo": "descuento_platillos", "valor": 20, "tipo_platillo": "Bebida"}',
 'imagenes/promociones/happy_hour.jpg'
),

('Combo Familiar', 
 '10% de descuento en platos principales para grupos de 4 o más personas.', 
 10.00, 
 'Activo', 
 '{"tipo": "cantidad_personas", "minimo": 4, "tipo_platillo": "Plato Principal"}', 
 '{"tipo": "descuento_platillos", "valor": 10, "tipo_platillo": "Plato Principal"}',
 'imagenes/promociones/combo_familiar.jpg'
),

('Menú Infantil Gratis', 
 'Un menú infantil gratis por la compra de dos platos principales.', 
 100.00, 
 'Activo', 
 '{"tipo": "cantidad_platillos", "minimo": 2, "tipo_platillo": "Plato Principal"}', 
 '{"tipo": "platillo_gratis", "tipo_platillo": "Menú Infantil"}',
 'imagenes/promociones/menu_infantil.jpg'
);

-- Insertar datos en la tabla `Mesa`
INSERT INTO Mesa (cantidad_asientos, estado) VALUES
(4, 'Disponible'),
(2, 'Reservada'),
(6, 'Ocupada'),
(4, 'Disponible'),
(2, 'Para Limpiar');

-- Insertar datos en la tabla `Reserva`
INSERT INTO Reserva (nombre_reserva, apellido_reserva, cantidad_personas, hora, fecha, id_mesa, estado_reserva) VALUES
('Ana', 'Gómez', 4, '19:00:00', '2024-09-15', 1, 'Pendiente'),
('Luis', 'Martínez', 2, '20:00:00', '2024-09-15', 2, 'Realizada'),
('Pedro', 'Hernández', 6, '21:00:00', '2024-09-15', 3, 'Completada'),
('Laura', 'Rodríguez', 4, '18:00:00', '2024-09-15', 4, 'Cancelada'),
('Carlos', 'Fernández', 2, '22:00:00', '2024-09-15', 5, 'Pendiente');

-- Insertar datos en la tabla `Ingredientes`
INSERT INTO Ingredientes (nombre, cantidad, precio, unidad_medida) VALUES
('Tomate', 50.00, 2.00, 'kg'),
('Lechuga', 30.00, 1.50, 'kg'),
('Carne de Res', 100.00, 10.00, 'kg'),
('Queso', 20.00, 5.00, 'kg'),
('Pollo', 70.00, 7.00, 'kg');

-- Insertar datos en la tabla `Platillos`
INSERT INTO Platillos (nombre_platillo, descripcion_platillo, precio, estado, tiempo_preparacion, ruta_foto, tipo_platillo) VALUES
('Ensalada César', 'Ensalada con pollo, queso parmesano y aderezo César.', 12.00, 'Disponible', '00:15:00', 'ensalada_cesar.jpg', 'Entrada'),
('Pizza Margarita', 'Pizza con tomate, mozzarella y albahaca.', 15.00, 'Disponible', '00:30:00', 'pizza_margarita.jpg', 'Plato Principal'),
('Pasta Alfredo', 'Pasta con salsa Alfredo y pollo.', 14.00, 'Disponible', '00:25:00', 'pasta_alfredo.jpg', 'Plato Principal'),
('Brownie', 'Brownie de chocolate con nueces.', 6.00, 'Disponible', '00:10:00', 'brownie.jpg', 'Postres'),
('Limonada', 'Limonada fresca y natural.', 5.00, 'Disponible', '00:05:00', 'limonada.jpg', 'Bebida');

-- Insertar datos en la tabla `Platillo_Ingrediente`
INSERT INTO Platillo_Ingrediente (id_platillo, id_ingrediente, cantidad_utilizada) VALUES
(1, 1, 0.20),
(1, 2, 0.10),
(2, 1, 0.30),
(2, 3, 0.50),
(3, 3, 0.70);

-- Insertar datos en la tabla `Pedido`
INSERT INTO Pedido (id_mesa, id_usuario, total_cuenta, hora, fecha, estado, tipo) VALUES
(1, 1, 50.00, '13:00:00', '2024-09-01', 'Listo', 'Para Servir'),
(2, 2, 75.00, '13:30:00', '2024-09-01', 'Pagado', 'Para Llevar'),
(3, 3, 100.00, '14:00:00', '2024-09-01', 'En Cocina', 'Delivery'),
(4, 4, 60.00, '14:30:00', '2024-09-01', 'Servido', 'Para Servir'),
(5, 5, 90.00, '15:00:00', '2024-09-01', 'En Preparación', 'Para Llevar');

-- Insertar datos en la tabla `Detalle_Pedido_Platillo`
INSERT INTO Detalle_Pedido_Platillo (id_pedido, id_platillo, cantidad) VALUES
(1, 1, 2),
(1, 5, 1),
(2, 2, 1),
(2, 3, 2),
(3, 4, 3);

-- Insertar datos en la tabla `historial_pedidos`
INSERT INTO historial_pedidos (id_pedido, id_mesa, id_usuario, total_cuenta, hora, fecha, estado, tipo) VALUES
(1, 1, 1, 100.00, '12:30:00', '2024-09-01', 'Listo', 'Para Servir'),
(2, 2, 2, 150.00, '13:00:00', '2024-09-01', 'Pagado', 'Para Llevar'),
(3, 3, 3, 200.00, '13:30:00', '2024-09-01', 'En Cocina', 'Delivery'),
(4, 4, 4, 250.00, '14:00:00', '2024-09-01', 'Servido', 'Para Servir'),
(5, 5, 5, 300.00, '14:30:00', '2024-09-01', 'En Preparación', 'Para Llevar');