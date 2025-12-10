-- BLOQUE A: CREACIÓN DE TABLAS (ESTRUCTURA)

-- 1. TABLA DE CLIENTES
CREATE TABLE Clientes (
    ID_Cliente INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Telefono VARCHAR(15)
);

-- 2. TABLA DE INVENTARIO DE CANOAS
CREATE TABLE Canoas (
    ID_Canoa INT AUTO_INCREMENT PRIMARY KEY,
    Tipo VARCHAR(50) NOT NULL,
    Modelo VARCHAR(50),
    Capacidad_Pers INT NOT NULL,
    Estado ENUM('Disponible', 'Alquilada', 'Mantenimiento') DEFAULT 'Disponible'
);

-- 3. TABLA DE RESERVAS
CREATE TABLE Reservas (
    ID_Reserva INT AUTO_INCREMENT PRIMARY KEY,
    ID_Canoa INT,
    ID_Cliente INT,
    Fecha_Inicio DATETIME NOT NULL,
    Fecha_Fin DATETIME NOT NULL,
    Precio_Total DECIMAL(10, 2),
    FOREIGN KEY (ID_Canoa) REFERENCES Canoas(ID_Canoa),
    FOREIGN KEY (ID_Cliente) REFERENCES Clientes(ID_Cliente)
);


-- BLOQUE B: INVENTARIO INICIAL (DATOS)

-- 10 Canoas Dobles
INSERT INTO Canoas (Tipo, Modelo, Capacidad_Pers, Estado) VALUES 
('doble', 'K2-RíoPro', 2, 'Disponible'),
('doble', 'K2-RíoPro', 2, 'Disponible'),
('doble', 'K2-RíoPro', 2, 'Disponible'),
('doble', 'K2-RíoPro', 2, 'Disponible'),
('doble', 'K2-RíoPro', 2, 'Disponible'),
('doble', 'K2-RíoEco', 2, 'Disponible'),
('doble', 'K2-RíoEco', 2, 'Disponible'),
('doble', 'K2-RíoEco', 2, 'Disponible'),
('doble', 'K2-RíoEco', 2, 'Disponible'),
('doble', 'K2-RíoEco', 2, 'Disponible');

-- 5 Canoas Individuales
INSERT INTO Canoas (Tipo, Modelo, Capacidad_Pers, Estado) VALUES 
('individual', 'K1-Veloz', 1, 'Disponible'),
('individual', 'K1-Veloz', 1, 'Disponible'),
('individual', 'K1-Iniciación', 1, 'Disponible'),
('individual', 'K1-Iniciación', 1, 'Disponible'),
('individual', 'K1-Iniciación', 1, 'Disponible');

-- 5 Canoas Familiares/Triples
INSERT INTO Canoas (Tipo, Modelo, Capacidad_Pers, Estado) VALUES 
('triple', 'Familiar-3p', 3, 'Disponible'),
('triple', 'Familiar-3p', 3, 'Disponible'),
('triple', 'Familiar-4p', 4, 'Disponible'),
('triple', 'Familiar-4p', 4, 'Disponible'),
('triple', 'Familiar-4p', 4, 'Disponible');