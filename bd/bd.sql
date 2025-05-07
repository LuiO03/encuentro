DROP DATABASE IF EXISTS encuentro;
CREATE DATABASE encuentro;
USE encuentro;

CREATE TABLE banners (
    id_banner INT AUTO_INCREMENT PRIMARY KEY,
    pagina VARCHAR(50) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    titulo VARCHAR(100),
    descripcion TEXT
);

INSERT INTO banners (pagina, imagen, titulo, descripcion) VALUES
('inicio', 'banner01.jpg', 'Bienvenidos al Restaurante', 'Descubre una experiencia culinaria única con ingredientes frescos y sabores auténticos.'),
('inicio', 'banner02.jpg', 'Sabores que Enamoran', 'Explora nuestro menú variado y disfruta de platos preparados con pasión y dedicación.'),
('inicio', 'banner03.jpg', 'Ambiente Acogedor', 'Relájate y disfruta de una comida excepcional en un ambiente cálido y familiar.'),
('menu', 'banner01.jpg', 'Nuestro Menú', 'Desde entradas hasta postres, descubre una amplia selección de platos deliciosos para todos los gustos.'),
('nosotros', 'banner02.jpg', 'Nuestra Historia', 'Con años de experiencia, nuestro restaurante se ha convertido en un referente de la gastronomía local.'),
('contactos', 'banner03.jpg', 'Contáctanos', '¿Tienes preguntas o sugerencias? Estamos aquí para atenderte y hacer de tu visita una experiencia inolvidable.');

CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO mensajes (nombre, apellido, telefono, correo, mensaje) VALUES
('Juan', 'Pérez', '987654321', 'juan.perez@example.com', 'Estoy interesado en conocer más sobre su menú.'),
('María', 'Gómez', '912345678', 'maria.gomez@example.com', '¿Tienen opciones vegetarianas en su restaurante?'),
('Carlos', 'López', '956789123', 'carlos.lopez@example.com', 'Quisiera hacer una reservación para este fin de semana.'),
('Ana', 'Martínez', '943216789', 'ana.martinez@example.com', '¿Cuáles son los métodos de pago que aceptan?'),
('Luis', 'Fernández', '967891234', 'luis.fernandez@example.com', '¿Ofrecen servicio de catering para eventos?');

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    dni VARCHAR(15) UNIQUE,
    correo VARCHAR(100) UNIQUE,
    telefono VARCHAR(15),
    direccion TEXT,
    password VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT 'default_admin.jpg',
    rol ENUM('Administrador', 'Empleado', 'Cliente'),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nombre, apellido, dni, correo, telefono, direccion, password, foto_perfil, rol) VALUES
('Lui', 'Osorio', '12345678', 'luiosorio@email.com', '987654321', 'Av. Principal 123, Lima', '$2y$10$fY7pQWv0GQy87/iFJ1WdNuMl2aEGw7ISOecta/FbWfPMY4FO2WO9i', 'pikachu.jpg', 'Administrador'),
('Juan', 'Pérez', '87654321', 'juan.perez@email.com', '998877665', 'Calle Secundaria 456, Arequipa', '$2y$10$fY7pQWv0GQy87/iFJ1WdNuMl2aEGw7ISOecta/FbWfPMY4FO2WO9i', 'user.png', 'Administrador'),
('María', 'López', '45678912', 'maria.lopez@email.com', '976543210', 'Jr. Comercio 789, Cusco', '$2y$10$fY7pQWv0GQy87/iFJ1WdNuMl2aEGw7ISOecta/FbWfPMY4FO2WO9i', 'user.png', 'Empleado'),
('Carlos', 'Gómez', '32165498', 'carlos.gomez@email.com', '965432109', 'Pasaje Libertad 321, Trujillo', '$2y$10$fY7pQWv0GQy87/iFJ1WdNuMl2aEGw7ISOecta/FbWfPMY4FO2WO9i', 'user.png', 'Empleado'),
('Ana', 'Torres', '78912345', 'ana.torres@email.com', '954321098', 'Av. Los Héroes 567, Piura', '$2y$10$fY7pQWv0GQy87/iFJ1WdNuMl2aEGw7ISOecta/FbWfPMY4FO2WO9i', 'user.png', 'Administrador'),
('Luis', 'Mendoza', '98765432', 'luis.mendoza@email.com', '943210987', 'Calle San Martín 654, Chiclayo', '$2y$10$fY7pQWv0GQy87/iFJ1WdNuMl2aEGw7ISOecta/FbWfPMY4FO2WO9i', 'user.png', 'Empleado'),
('Pedro', 'Ramírez', '15975346', 'pedro.ramirez@email.com', '912345678', 'Av. Industrial 789, Lima', '$2y$10$fY7pQWv0GQy87/iFJ1WdNuMl2aEGw7ISOecta/FbWfPMY4FO2WO9i', 'user.png', 'Cliente'),
('Laura', 'Fernández', '75315982', 'laura.fernandez@email.com', '901234567', 'Calle Primavera 321, Cusco', '$2y$10$fY7pQWv0GQy87/iFJ1WdNuMl2aEGw7ISOecta/FbWfPMY4FO2WO9i', 'user.png', 'Cliente');

CREATE TABLE locales (
    id_local INT AUTO_INCREMENT PRIMARY KEY,
    nombre_local VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    celular VARCHAR(20),
    hora_abierto TIME NOT NULL,
    hora_cerrado TIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO locales (nombre_local, direccion, telefono, celular, hora_abierto, hora_cerrado) 
VALUES 
('Sucursal Centro', 'Av. Principal 123, Ciudad', '123456789', '987654321', '08:00:00', '22:00:00'),
('Sucursal Norte', 'Calle Secundaria 456, Ciudad', '234567890', '876543210', '09:00:00', '23:00:00');

CREATE TABLE restaurant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rest VARCHAR(100) NOT NULL,
    hora_abierto TIME NOT NULL,
    hora_cerrado TIME NOT NULL,
    direccion VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    celular VARCHAR(20) NOT NULL
);

INSERT INTO restaurant (nombre_rest, hora_abierto, hora_cerrado, direccion, correo, telefono, celular) 
VALUES ('Encuentro','08:00:00', '22:00:00', 'Av. Principal 123, Ciudad', 'contacto@restaurante.com', '123456789', '987654321');

CREATE TABLE social_media (
    id_red INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icono VARCHAR(50) NOT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    restaurant_id INT NOT NULL,
    FOREIGN KEY (restaurant_id) REFERENCES restaurant(id) ON DELETE CASCADE
);

INSERT INTO social_media (nombre, url, icono, estado, restaurant_id) VALUES 
('Facebook', 'https://facebook.com/mi-restaurante', 'fab fa-facebook', 1, 1),
('Instagram', 'https://instagram.com/mi-restaurante', 'fab fa-instagram', 1, 1),
('TikTok', 'https://tiktok.com/@mi-restaurante', 'fab fa-tiktok', 1, 1),
('Twitter', 'https://twitter.com/mi-restaurante', 'fab fa-twitter', 1, 1),
('YouTube', 'https://youtube.com/mi-restaurante', 'fab fa-youtube', 0, 1);

CREATE TABLE categorias (
    id_categoria INT(4) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    nombre_categoria VARCHAR(50) NOT NULL,
    imagen VARCHAR(255),
    descripcion TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre_producto VARCHAR(50) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    precio DECIMAL(10, 2) NOT NULL,
    ingredientes TEXT NOT NULL,
    disponible BOOLEAN DEFAULT 1, 
    id_categoria INT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    destacado TINYINT(1) DEFAULT 0,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE carrito (
    id_carrito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    estado ENUM('pendiente', 'finalizado', 'cancelado') NOT NULL DEFAULT 'pendiente',
    tipo_entrega ENUM('delivery', 'retiro') NOT NULL DEFAULT 'delivery',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_pedido DECIMAL(10,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

CREATE TABLE carrito_detalle (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_carrito INT,
    id_producto INT,
    cantidad INT NOT NULL DEFAULT 1 CHECK (cantidad > 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    precio_total DECIMAL(10,2),
    FOREIGN KEY (id_carrito) REFERENCES carrito(id_carrito) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
);

CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    fecha_reserva DATE NOT NULL,
    hora_reserva TIME NOT NULL,
    num_personas INT NOT NULL CHECK (num_personas > 0),
    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente',
    comentario TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

CREATE TABLE mesas (
    id_mesa INT AUTO_INCREMENT PRIMARY KEY,
    nombre_mesa VARCHAR(50) NOT NULL,
    capacidad INT NOT NULL CHECK (capacidad > 0),
    estado ENUM('disponible', 'reservada', 'ocupada') DEFAULT 'disponible'
);

CREATE TABLE reserva_mesa (
    id_reserva_mesa INT AUTO_INCREMENT PRIMARY KEY,
    id_reserva INT,
    id_mesa INT,
    FOREIGN KEY (id_reserva) REFERENCES reservas(id_reserva) ON DELETE CASCADE,
    FOREIGN KEY (id_mesa) REFERENCES mesas(id_mesa) ON DELETE CASCADE
);

-- Insertar mesas
INSERT INTO mesas (nombre_mesa, capacidad, estado) VALUES
('Mesa 1', 8, 'reservada'),
('Mesa 2', 8, 'reservada'),
('Mesa 3', 8, 'reservada'),
('Mesa 4', 8, 'reservada'),
('Mesa 5', 8, 'reservada'),
('Mesa 6', 8, 'reservada'),
('Mesa 7', 8, 'ocupada'),
('Mesa 8', 8, 'ocupada'),
('Mesa 9', 8, 'disponible'),
('Mesa 10', 8, 'disponible');

-- Insertar reservas
INSERT INTO reservas (id_usuario, fecha_reserva, hora_reserva, num_personas, estado, comentario) VALUES
(7, '2025-04-01', '19:00:00', 2, 'confirmada', 'Mesa cerca de la ventana.'),
(2, '2025-04-02', '20:30:00', 4, 'pendiente', 'Cumpleaños sorpresa.'),
(3, '2025-04-03', '18:00:00', 6, 'confirmada', 'Prefiere mesa al fondo.'),
(4, '2025-04-04', '21:00:00', 2, 'cancelada', 'Canceló por enfermedad.'),
(5, '2025-04-05', '19:30:00', 8, 'confirmada', 'Evento especial.');

-- Asignar mesas a reservas
INSERT INTO reserva_mesa (id_reserva, id_mesa) VALUES
(1, 1), -- Reserva 1 usa mesa 1
(2, 2), -- Reserva 2 usa mesa 2
(3, 3), -- Reserva 3 usa mesa 3
(4, 4), -- Reserva 4 usa mesa 4
(5, 5); -- Reserva 5 usa mesa 5


INSERT INTO categorias (nombre_categoria, imagen, descripcion) VALUES
('Entradas', 'entradas.png', 'Platos ligeros para comenzar la comida.'),
('Plato Fuerte', 'platos_fuerte.jpg', 'Platos principales con proteínas y acompañamientos.'),
('Postres', 'postres.jpg', 'Dulces y postres tradicionales.'),
('Bebidas', 'bebidas.jpg', 'Bebidas refrescantes y naturales.'),
('Pizzas', 'pizzas.jpg', 'Variedad de pizzas con diferentes ingredientes.'),
('Sopas', 'sopas.jpg', 'Sopas calientes y cremas para cualquier ocasión.');

INSERT INTO productos (nombre_producto, descripcion, imagen, precio, ingredientes, disponible, id_categoria,fecha_creacion, destacado) VALUES
-- Entradas
('Ceviche Peruano', 'Delicioso ceviche preparado con pescado fresco, jugo de limón, ají y cebolla morada.', 'ceviche_peruano.jpg', 12.00, 'Pescado fresco, limón, ají, cebolla morada, choclo, camote', 1, 1,NOW(),1),
('Anticuchos', 'Brochetas de corazón de res marinadas con especias peruanas, servidas con papa dorada.', 'anticuchos.png', 10.00, 'Corazón de res, ají panca, vinagre, ajo, papa dorada', 1, 1,NOW(),0),
('Papa a la Huancaína', 'Rodajas de papa bañadas en una cremosa salsa de ají amarillo, acompañadas con huevo y aceituna.', 'huancaína.jpg', 8.00, 'Papa, ají amarillo, galleta, leche evaporada, queso fresco, huevo, aceituna', 1, 1,NOW(),1),
('Choclo con Queso', 'Delicioso choclo tierno acompañado de queso fresco serrano.', 'choclo_con_queso.png', 7.00, 'Choclo, queso fresco', 1, 1,NOW(),1),
('Leche de Tigre', 'Refrescante concentrado cítrico del ceviche, servido en vaso con trozos de pescado, cebolla y maíz.', 'leche_de_tigre.webp', 10.00, 'Jugo de limón, pescado, cebolla, ají, sal, maíz, cilantro', 1, 1, NOW(), 1),

-- Plato Fuerte
('Lomo Saltado', 'Jugoso lomo de res salteado con cebolla, tomate y especias peruanas, servido con papas fritas y arroz.', 'lomo_saltado.jpg', 15.00, 'Lomo de res, cebolla, tomate, especias, papas fritas, arroz', 1, 2,NOW(),0),
('Arroz con Pato', 'Arroz verde cocido con culantro y trozos de pato macerado en chicha de jora.', 'pato.jpg', 18.00, 'Arroz, pato, chicha de jora, culantro, especias', 1, 2,NOW(),1),
('Ají de Gallina', 'Delicioso ají de gallina en salsa cremosa con ají amarillo, servido con arroz blanco.', 'gallina.jpg', 14.00, 'Pechuga de pollo, ají amarillo, pan, leche evaporada, queso parmesano, arroz', 1, 2,NOW(),1),
('Tacu Tacu con Lomo', 'Mezcla de frejoles y arroz dorados a la plancha, acompañado con jugoso lomo salteado.', 'tacu_tacu_con_lomo.jpg', 17.00, 'Frejoles, arroz, lomo de res, cebolla, tomate, especias', 1, 2,NOW(),0),
('Cuy Chactado', 'Tradicional cuy crocante frito al estilo andino, servido con papas doradas y ensalada.', 'cuy_chactado.jpg', 20.00, 'Cuy, papas, ajo, sal, ensalada', 1, 2, NOW(), 1),
('Causa Rellena', 'Capa de papa amarilla prensada con limón y ají, rellena de pollo o atún, servida fría.', 'causa_rellena.jpg', 12.00, 'Papa amarilla, limón, ají amarillo, pollo o atún, mayonesa', 1, 2, NOW(), 1),
('Seco de Res', 'Guiso tradicional de carne de res con culantro, acompañado de arroz blanco y yuca sancochada.', 'seco.webp', 16.00, 'Carne de res, culantro, chicha de jora, arroz, yuca', 1, 2, NOW(), 0),
('Arroz a la Cubana', 'Arroz blanco acompañado de plátano frito, huevo y salsa de tomate.', 'cubana.webp', 11.00, 'Arroz, plátano, huevo, salsa de tomate', 1, 2, NOW(), 1),

-- Postres
('Torta de Chocolate', 'Esponjosa torta de chocolate con cobertura de ganache de cacao.', 'tota_chocolate.jpg', 7.00, 'Harina, cacao, azúcar, mantequilla, crema de leche', 1, 3,NOW(),0),
('Suspiro Limeño', 'Clásico postre limeño con manjar blanco y merengue italiano.', 'suspiro.jpg', 8.00, 'Leche condensada, azúcar, huevo, canela', 1, 3,NOW(),0),
('Arroz con Leche', 'Postre casero de arroz cocido en leche con canela y clavo de olor.', 'arroz_leche.jpg', 6.00, 'Arroz, leche, canela, clavo de olor, azúcar', 1, 3,NOW(),1),
('Mazamorra Morada', 'Deliciosa mazamorra hecha con maíz morado, frutas y especias.', 'mazamorra_morada.webp', 6.00, 'Maíz morado, piña, canela, clavo de olor, azúcar', 1, 3,NOW(),1),

-- Bebidas
('Chicha Morada', 'Bebida refrescante a base de maíz morado con piña, canela y clavo de olor.', 'chicha.jpg', 6.00, 'Maíz morado, piña, canela, clavo de olor, azúcar', 1, 4,NOW(),1),
('Jugo de Naranja', 'Jugo natural de naranja recién exprimido.', 'jugo_naranja.jpg', 5.00, 'Naranja, azúcar opcional', 1, 4,NOW(),1),
('Maracuyá Sour', 'Coctel a base de pisco, maracuyá y un toque de jarabe de goma.', 'maracuya_sour.jpg', 10.00, 'Pisco, maracuyá, jarabe de goma, clara de huevo', 1, 4,NOW(),0),
('Emoliente', 'Bebida caliente a base de cebada, linaza y hierbas medicinales.', 'emoliente.jpg', 4.00, 'Cebada, linaza, boldo, cola de caballo', 1, 4,NOW(),1),

-- Pizzas
('Pizza Napolitana', 'Pizza con salsa de tomate, mozzarella, albahaca y aceite de oliva.', 'pizza_napolitana.jpg', 18.00, 'Masa, tomate, mozzarella, albahaca, aceite de oliva', 1, 5,NOW(),1),
('Pizza Hawaiana', 'Clásica pizza con jamón y piña sobre una base de mozzarella.', 'pizza_hawaiana.jpg', 20.00, 'Masa, tomate, mozzarella, jamón, piña', 1, 5,NOW(),1),
('Pizza Pepperoni', 'Deliciosa pizza con rodajas de pepperoni y queso derretido.', 'pizza_pepperoni.jpg', 19.00, 'Masa, tomate, mozzarella, pepperoni', 1, 5,NOW(),0),

-- Sopas
('Sopa Criolla', 'Sopa caliente con carne de res, fideos y un toque de leche evaporada.', 'sopa_criolla.jpg', 12.00, 'Carne de res, fideos, ají panca, leche evaporada', 1, 6,NOW(),0),
('Caldo de Gallina', 'Caldo nutritivo con gallina, fideos, huevo y papas.', 'caldo_gallina.jpg', 13.00, 'Gallina, fideos, huevo, papa, cebolla china', 1, 6,NOW(),1),
('Sancochado', 'Plato tradicional con carne de res, verduras y papa en un caldo sustancioso.', 'sancochado.jpg', 15.00, 'Carne de res, yuca, papa, zanahoria, repollo', 1, 6,NOW(),0);

DELIMITER $$

CREATE TRIGGER actualizar_total_carrito
AFTER INSERT ON carrito_detalle
FOR EACH ROW
BEGIN
    UPDATE carrito 
    SET total_pedido = (
        SELECT SUM(precio_total) FROM carrito_detalle WHERE id_carrito = NEW.id_carrito
    )
    WHERE id_carrito = NEW.id_carrito;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER actualizar_total_carrito_update
AFTER UPDATE ON carrito_detalle
FOR EACH ROW
BEGIN
    UPDATE carrito 
    SET total_pedido = (
        SELECT SUM(precio_total) FROM carrito_detalle WHERE id_carrito = NEW.id_carrito
    )
    WHERE id_carrito = NEW.id_carrito;
END$$

DELIMITER ;

DELIMITER $$
CREATE TRIGGER actualizar_total_carrito_delete
AFTER DELETE ON carrito_detalle
FOR EACH ROW
BEGIN
    UPDATE carrito 
    SET total_pedido = (
        SELECT COALESCE(SUM(precio_total), 0) FROM carrito_detalle WHERE id_carrito = OLD.id_carrito
    )
    WHERE id_carrito = OLD.id_carrito;
END$$

DELIMITER ;
