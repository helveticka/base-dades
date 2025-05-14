-- Crear la base de dades
CREATE DATABASE FrescINatural;
USE FrescINatural;

-- Crear la taula ILLA
CREATE TABLE ILLA (
    idIlla INT PRIMARY KEY,
    nom_illa VARCHAR(64) NOT NULL
);

-- Crear la taula CATEGORIA
CREATE TABLE CATEGORIA (
    idCategoria INT PRIMARY KEY,
    categoria VARCHAR(64) NOT NULL
);

-- Crear la taula ILLA
CREATE TABLE ZONA (
    idZona INT PRIMARY KEY,
    nomZona VARCHAR(64) NOT NULL,
    idIlla INT NOT NULL,
    CONSTRAINT fk_zona_illa FOREIGN KEY (idIlla) REFERENCES ILLA(idIlla)
);

-- Crear la taula DIRECCIO_MUNICIPI
CREATE TABLE DIRECCIO_MUNICIPI (
    idDireccióMunicipi INT PRIMARY KEY
);

-- Crear la taula ADRECA
CREATE TABLE ADRECA (
    idAdreca INT PRIMARY KEY,
    carrer VARCHAR(128) NOT NULL,
    numero INT NOT NULL,
    adicional VARCHAR(128),
    idDireccioMunicipi INT NOT NULL,
    CONSTRAINT fk_adreca_direccioMunicipi FOREIGN KEY (idDireccioMunicipi) REFERENCES DIRECCIO_MUNICIPI(idDireccióMunicipi)
);

-- Crear la taula MAGATZEM
CREATE TABLE MAGATZEM (
    idMagatzem INT PRIMARY KEY,
    nom VARCHAR(128) NOT NULL,
    capacitat INT NOT NULL,
    idAdreca INT,
    CONSTRAINT fk_magatzem_adreca FOREIGN KEY (idAdreca) REFERENCES ADRECA(idAdreca)
);

-- Crear la taula MUNICIPI
CREATE TABLE MUNICIPI (
    idMunicipi INT PRIMARY KEY,
    nomMunicipi VARCHAR(64) NOT NULL,
    superficie DECIMAL(10, 2) NOT NULL,
    poblacio INT NOT NULL,
    idZona INT NOT NULL,
    idMagatzem INT NOT NULL,    
    CONSTRAINT fk_municipi_zona FOREIGN KEY (idZona) REFERENCES ZONA(idZona),
    CONSTRAINT fk_municipi_magatzem FOREIGN KEY (idMagatzem) REFERENCES MAGATZEM(idMagatzem)
);



-- Crear la taula PROVEIDOR
CREATE TABLE PROVEIDOR (
    idProveidor INT PRIMARY KEY,
    nomProveidor VARCHAR(56) NOT NULL,
    raoSocial VARCHAR(128),
    cif VARCHAR(16) NOT NULL,
    tempsLliurament INT NOT NULL,
    idAdrecaFiscal INT,
    idAdrecaDistribucio INT,
    CONSTRAINT fk_proveidor_adrecaFiscal FOREIGN KEY (idAdrecaFiscal) REFERENCES ADRECA(idAdreca),
    CONSTRAINT fk_proveidor_adrecaDistribucio FOREIGN KEY (idAdrecaDistribucio) REFERENCES ADRECA(idAdreca)
);

-- Crear la taula PRODUCTE
CREATE TABLE PRODUCTE (
    referencia INT PRIMARY KEY,
    nomComercial VARCHAR(54) NOT NULL,
    preuUnitari DECIMAL(4, 2) NOT NULL,
    descripcio TEXT,
    unitatMesura VARCHAR(24),
    formatVenda VARCHAR(24),
    idCategoria INT NOT NULL,
    idProveidor INT NOT NULL,
    CONSTRAINT fk_producte_categoria FOREIGN KEY (idCategoria) REFERENCES CATEGORIA(idCategoria),
    CONSTRAINT fk_producte_proveidor FOREIGN KEY (idProveidor) REFERENCES PROVEIDOR(idProveidor)
);

-- Crear la taula LOT
CREATE TABLE LOT (
    numeroLot INT PRIMARY KEY,
    quantitat INT NOT NULL,
    caducitat DATE NOT NULL,
    idMagatzem INT NOT NULL,
    idProducte INT NOT NULL,
    idProveidor INT NOT NULL,
    CONSTRAINT fk_lot_magatzem FOREIGN KEY (idMagatzem) REFERENCES MAGATZEM(idMagatzem),
    CONSTRAINT fk_lot_producte FOREIGN KEY (idProducte) REFERENCES PRODUCTE(referencia),
    CONSTRAINT fk_lot_proveidor FOREIGN KEY (idProveidor) REFERENCES PROVEIDOR(idProveidor)
);

-- Crear la taula PERSONA_RESPONSABLE
CREATE TABLE PERSONA_RESPONSABLE (
    idResponsable INT PRIMARY KEY,
    càrrec VARCHAR(64),
    dataInici DATE NOT NULL,
    dataFi DATE,
    nomResponsable VARCHAR(128) NOT NULL,
    telefonResponsable VARCHAR(32) NOT NULL,
    emailResponsable VARCHAR(128) NOT NULL,
    idMagatzem INT NOT NULL,
    CONSTRAINT fk_responsable_magatzem FOREIGN KEY (idMagatzem) REFERENCES MAGATZEM(idMagatzem)
);

-- Crear la taula VEHICLE
CREATE TABLE VEHICLE (
    matricula VARCHAR(16) PRIMARY KEY,
    marca VARCHAR(64),
    model VARCHAR(64),
    capacitat INT NOT NULL,
    idMagatzem INT NOT NULL,
    CONSTRAINT fk_vehicle_magatzem FOREIGN KEY (idMagatzem) REFERENCES MAGATZEM(idMagatzem)
);

-- Crear la taula CÀRREGA
CREATE TABLE CÀRREGA (
    idCarrega INT PRIMARY KEY,
    jornada DATE NOT NULL,
    idVehicle VARCHAR(16) NOT NULL,
    CONSTRAINT fk_carrega_vehicle FOREIGN KEY (idVehicle) REFERENCES VEHICLE(matricula)
);

-- Crear la taula QUANTITAT_CÀRREGA
CREATE TABLE QUANTITAT_CÀRREGA (
    idQCarrega INT PRIMARY KEY,
    unitats INT NOT NULL,
    idCarrega INT NOT NULL,
    numLot INT NOT NULL,
    CONSTRAINT fk_quantitatCarrega_carrega FOREIGN KEY (idCarrega) REFERENCES CÀRREGA(idCarrega),
    CONSTRAINT fk_quantitatCarrega_lot FOREIGN KEY (numLot) REFERENCES LOT(numeroLot)
);

-- Crear la taula VENEDOR
CREATE TABLE VENEDOR (
    idVenedor INT PRIMARY KEY,
    nomVenedor VARCHAR(128) NOT NULL,
    telefonVenedor VARCHAR(32),
    emailVenedor VARCHAR(128),
    idVehicle VARCHAR(16) NOT NULL,
    idMagatzem INT NOT NULL,
    CONSTRAINT fk_venedor_vehicle FOREIGN KEY (idVehicle) REFERENCES VEHICLE(matricula),
    CONSTRAINT fk_venedor_magatzem FOREIGN KEY (idMagatzem) REFERENCES MAGATZEM(idMagatzem)
);

-- Crear la taula VENDA
CREATE TABLE VENDA (
    idVenda INT PRIMARY KEY,
    data DATE NOT NULL,
    idVenedor INT NOT NULL,
    idClient VARCHAR(16) NOT NULL,
    CONSTRAINT fk_venda_venedor FOREIGN KEY (idVenedor) REFERENCES VENEDOR(idVenedor)
);

-- Crear la taula QUANTITAT_VENUDA (antes R_VENDA_QUANTITAT_CARREGA)
CREATE TABLE QUANTITAT_VENUDA (
    idVenda INT,
    idQCarrega INT,
    unitats INT NOT NULL,
    PRIMARY KEY (idVenda, idQCarrega),
    CONSTRAINT fk_qvenuda_venda FOREIGN KEY (idVenda) REFERENCES VENDA(idVenda),
    CONSTRAINT fk_qvenuda_qcarrega FOREIGN KEY (idQCarrega) REFERENCES QUANTITAT_CÀRREGA(idQCarrega)
);

-- Crear la taula PARTICULAR
CREATE TABLE PARTICULAR (
    idFiscal VARCHAR(16) PRIMARY KEY,
    nomParticular VARCHAR(128) NOT NULL,
    telefonParticular VARCHAR(32) NOT NULL,
    emailParticular VARCHAR(128) NOT NULL
);

-- Crear la taula EMPRESA
CREATE TABLE EMPRESA (
    idFiscal VARCHAR(16) PRIMARY KEY,
    nomEmpresa VARCHAR(128) NOT NULL,
    terminiPagament INT NOT NULL,
    raoSocial VARCHAR(128),
    idAdreca INT NOT NULL,
    CONSTRAINT fk_empresa_adreca FOREIGN KEY (idAdreca) REFERENCES ADRECA(idAdreca)
);

-- Crear la taula PERSONA_CONTACTE
CREATE TABLE PERSONA_CONTACTE (
    idPersonaContacte INT PRIMARY KEY,
    idEmpresa VARCHAR(16) NOT NULL,
    nomPersonaContacte VARCHAR(128) NOT NULL,
    telefonPersonaContacte VARCHAR(32),
    emailPersonaContacte VARCHAR(128),
    CONSTRAINT fk_personaContacte_empresa FOREIGN KEY (idEmpresa) REFERENCES EMPRESA(idFiscal)
);

-- Crear la taula PERSONA_REFERENCIA
CREATE TABLE PERSONA_REFERENCIA (
    idPersonaReferencia INT PRIMARY KEY,
    nomPersonaReferencia VARCHAR(128) NOT NULL,
    telefonPersonaReferencia VARCHAR(32),
    emailPersonaReferencia VARCHAR(128),
    idProveidor INT NOT NULL,
    CONSTRAINT fk_personaReferencia_proveidor FOREIGN KEY (idProveidor) REFERENCES PROVEIDOR(idProveidor)
);

-- Crear la taula R_ADRECA_PARTICULAR
CREATE TABLE R_ADRECA_PARTICULAR (
    idParticular VARCHAR(16),
    idAdreca INT,
    PRIMARY KEY (idParticular, idAdreca),
    CONSTRAINT fk_r_adreca_particular_particular FOREIGN KEY (idParticular) REFERENCES PARTICULAR(idFiscal),
    CONSTRAINT fk_r_adreca_particular_adreca FOREIGN KEY (idAdreca) REFERENCES ADRECA(idAdreca)
);

-- Insertar Illes
INSERT INTO ILLA (idIlla, nom_illa) 
VALUES 
(1, 'Mallorca'),
(2, 'Menorca'),
(3, 'Eivissa'),
(4, 'Formentera');

-- Insertar Categorías
INSERT INTO CATEGORIA (idCategoria, categoria) 
VALUES 
(1, 'Peix'),
(2, 'Carn'),
(3, 'Gelats'),
(4, 'Plats preparats');

-- Insertar Zones
INSERT INTO ZONA (idZona, nomZona, idIlla) 
VALUES 
(1, 'Zona Nord', 1),
(2, 'Zona Sud', 1),
(3, 'Zona Est', 2),
(4, 'Zona Oest', 3);

-- Insertar Direcciones Municipales
INSERT INTO DIRECCIO_MUNICIPI (idDireccióMunicipi) 
VALUES 
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(9),
(10);

-- Insertar Direcciones
INSERT INTO ADRECA (idAdreca, carrer, numero, adicional, idDireccioMunicipi) 
VALUES 
(1, 'Carrer Mallorca', 1, 'Edifici A', 1),
(2, 'Carrer Catalunya', 12, 'Piso 3', 2),
(3, 'Carrer de la Pau', 25, 'Local 2', 3),
(4, 'Carrer Ibiza', 7, 'Piso 2', 4),
(5, 'Carrer Gran', 50, 'Oficina 1', 5),
(6, 'Carrer Santa Eulària', 5, 'Edifici B', 6),
(7, 'Carrer Muro', 15, 'Piso 4', 7),
(8, 'Carrer Ciutadella', 18, 'Planta Baja', 8),
(9, 'Carrer Sant Antoni', 22, 'Oficina 3', 9),
(10, 'Carrer Sant Llorenç', 8, 'Piso 2', 10);

-- Insertar Magatzems
INSERT INTO MAGATZEM (idMagatzem, nom, capacitat, idAdreca) 
VALUES 
(1, 'Magatzem Palma', 1000, 1),
(2, 'Magatzem Alcúdia', 500, 2),
(3, 'Magatzem Mahón', 800, 3),
(4, 'Magatzem Eivissa', 1200, 4),
(5, 'Magatzem Santa Eulària', 600, 5),
(6, 'Magatzem Manacor', 700, 6),
(7, 'Magatzem Muro', 400, 7),
(8, 'Magatzem Ciutadella', 500, 8),
(9, 'Magatzem Sant Antoni', 900, 9),
(10, 'Magatzem Sant Llorenç', 450, 10);

-- Insertar Municipis
INSERT INTO MUNICIPI (idMunicipi, nomMunicipi, superficie, poblacio, idZona, idMagatzem) 
VALUES 
(1, 'Palma', 308.99, 416000, 1, 1),
(2, 'Alcúdia', 60.10, 20000, 1, 2),
(3, 'Mahón', 30.10, 29000, 2, 3),
(4, 'Ibiza', 572.56, 130000, 3, 4),
(5, 'Santa Eulària', 125.47, 35000, 3, 5),
(6, 'Manacor', 105.25, 40000, 1, 6),
(7, 'Muro', 53.42, 15000, 1, 7),
(8, 'Ciutadella', 310.44, 27000, 2, 8),
(9, 'Sant Antoni', 121.02, 23000, 3, 9),
(10, 'Sant Llorenç', 85.15, 12000, 1, 10);

-- Insertar Proveïdors
INSERT INTO PROVEIDOR (idProveidor, nomProveidor, raoSocial, cif, tempsLliurament, idAdrecaFiscal, idAdrecaDistribucio) 
VALUES 
(1, 'Peixos del Nord S.L.', 'Peixos del Nord', 'A12345678', 5, 1, 2),
(2, 'Carns Puro S.A.', 'Carns Puro', 'B23456789', 3, 3, 4),
(3, 'Gelats Calents', 'Gelats Calents S.L.', 'C34567890', 7, 5, 6),
(4, 'Preparats Gourmet', 'Preparats Gourmet', 'D45678901', 10, 7, 8);

-- Insertar Productos
INSERT INTO PRODUCTE (referencia, nomComercial, preuUnitari, descripcio, unitatMesura, formatVenda, idCategoria, idProveidor) 
VALUES 
(101, 'Lluç Congelat', 5.99, 'Filet de lluç ultracongelat', 'kg', 'Caixa', 1, 1),
(102, 'Pollastre Sense Pells', 7.49, 'Pollastre fresc sense pells', 'kg', 'Unitat', 2, 2),
(103, 'Gelat de Xocolata', 2.99, 'Gelat de xocolata artesanal', 'L', 'Unitat', 3, 3),
(104, 'Plats Vegetals', 4.59, 'Plats preparats a base de verdures', 'kg', 'Caixa', 4, 4),
(105, 'Salmó Congelat', 12.49, 'Filet de salmó ultracongelat', 'kg', 'Caixa', 1, 1),
(106, 'Llamàntol', 15.99, 'Llamàntol ultracongelat', 'kg', 'Caixa', 1, 1),
(107, 'Hamburguesa de Vedella', 5.99, 'Hamburguesa de vedella fresca', 'Unitat', 'Unitat', 2, 2),
(108, 'Gelat de Vainilla', 2.49, 'Gelat de vainilla artesanal', 'L', 'Unitat', 3, 3),
(109, 'Tallarins de Marisc', 6.99, 'Tallarins amb marisc congelats', 'kg', 'Caixa', 4, 4),
(110, 'Filets de Peix', 4.19, 'Filets de peix blanc ultracongelat', 'kg', 'Caixa', 1, 1);

-- Insertar Lots
INSERT INTO LOT (numeroLot, quantitat, caducitat, idMagatzem, idProducte, idProveidor) 
VALUES 
(1, 100, '2025-12-31', 1, 101, 1),
(2, 150, '2025-06-30', 2, 102, 2),
(3, 80, '2025-08-15', 3, 103, 3),
(4, 200, '2025-07-10', 4, 104, 4);

-- Insertar Personas Responsables
INSERT INTO PERSONA_RESPONSABLE (idResponsable, càrrec, dataInici, dataFi, nomResponsable, telefonResponsable, emailResponsable, idMagatzem) 
VALUES 
(1, 'Gerent', '2024-01-01', NULL, 'Maria López', '600123456', 'maria.lopez@fresc.com', 1),
(2, 'Encargat', '2023-05-01', '2024-05-01', 'Antonio Fernández', '601234567', 'antonio.fernandez@fresc.com', 2),
(3, 'Supervisor', '2023-10-01', NULL, 'Pere Ruiz', '602345678', 'pere.ruiz@fresc.com', 3);

-- Insertar Vehículos
INSERT INTO VEHICLE (matricula, marca, model, capacitat, idMagatzem) 
VALUES 
('IB1234AB', 'Mercedes', 'Sprinter', 1000, 1),
('MA2345CD', 'Renault', 'Master', 800, 2),
('ME3456EF', 'Fiat', 'Ducato', 950, 3),
('EV4567GH', 'Ford', 'Transit', 1200, 4);

-- Insertar Càrregues
INSERT INTO CÀRREGA (idCarrega, jornada, idVehicle) 
VALUES 
(1, '2025-01-15', 'IB1234AB'),
(2, '2025-02-10', 'MA2345CD'),
(3, '2025-03-05', 'ME3456EF'),
(4, '2025-04-12', 'EV4567GH');

-- Insertar Quantitat Càrrega
INSERT INTO QUANTITAT_CÀRREGA (idQCarrega, unitats, idCarrega, numLot) 
VALUES 
(1, 100, 1, 1),
(2, 150, 2, 2),
(3, 80, 3, 3),
(4, 200, 4, 4);

-- Insertar Venedors
INSERT INTO VENEDOR (idVenedor, nomVenedor, telefonVenedor, emailVenedor, idVehicle, idMagatzem) 
VALUES 
(1, 'Joan Garcia', '600123456', 'joan.garcia@fresc.com', 'IB1234AB', 1),
(2, 'Pere Ruiz', '601234567', 'pere.ruiz@fresc.com', 'MA2345CD', 2),
(3, 'Maria López', '602345678', 'maria.lopez@fresc.com', 'ME3456EF', 3),
(4, 'Antonio Fernández', '603456789', 'antonio.fernandez@fresc.com', 'EV4567GH', 4);

-- Insertar Vendes
INSERT INTO VENDA (idVenda, data, idVenedor, idClient) 
VALUES 
(1, '2025-01-10', 1, 'F12345678'),
(2, '2025-02-15', 2, 'G23456789'),
(3, '2025-03-20', 3, 'H34567890'),
(4, '2025-04-25', 4, 'I45678901');

-- Insertar Quantitat Veneduda
INSERT INTO QUANTITAT_VENUDA (idVenda, idQCarrega, unitats) 
VALUES 
(1, 1, 10),
(2, 2, 15),
(3, 3, 8),
(4, 4, 20);

-- Insertar Clientes Particulares
INSERT INTO PARTICULAR (idFiscal, nomParticular, telefonParticular, emailParticular) 
VALUES 
('F12345678', 'Juan Pérez', '650123456', 'juan.perez@gmail.com'),
('G23456789', 'Ana Martínez', '651234567', 'ana.martinez@yahoo.com'),
('H34567890', 'Carlos Gómez', '652345678', 'carlos.gomez@hotmail.com'),
('I45678901', 'Lucía Fernández', '653456789', 'lucia.fernandez@gmail.com'),
('J56789012', 'David López', '654567890', 'david.lopez@yahoo.com');

-- Insertar Empresas
INSERT INTO EMPRESA (idFiscal, nomEmpresa, terminiPagament, raoSocial, idAdreca) 
VALUES 
('A12345678', 'Restaurante Can X', 30, 'Restaurante Can X S.L.', 1),
('B23456789', 'Hotel La Palma', 60, 'Hotel La Palma S.A.', 2),
('C34567890', 'Catering Isla Azul', 90, 'Catering Isla Azul S.L.', 3),
('D45678901', 'Restaurante Mar y Sol', 30, 'Restaurante Mar y Sol S.L.', 4),
('E56789012', 'Hotel Ibiza Dream', 60, 'Hotel Ibiza Dream S.A.', 5);

-- Insertar Personas Contacte
INSERT INTO PERSONA_CONTACTE (idPersonaContacte, idEmpresa, nomPersonaContacte, telefonPersonaContacte, emailPersonaContacte) 
VALUES 
(1, 'A12345678', 'Carlos Sánchez', '670123456', 'carlos.sanchez@restx.com'),
(2, 'B23456789', 'Laura Pérez', '671234567', 'laura.perez@hotelpalma.com'),
(3, 'C34567890', 'Sergio López', '672345678', 'sergio.lopez@cateringisla.com'),
(4, 'D45678901', 'Raquel Gómez', '673456789', 'raquel.gomez@restmar.com'),
(5, 'E56789012', 'José Martínez', '674567890', 'jose.martinez@ibizadream.com');

-- Insertar Personas Referencia
INSERT INTO PERSONA_REFERENCIA (idPersonaReferencia, nomPersonaReferencia, telefonPersonaReferencia, emailPersonaReferencia, idProveidor) 
VALUES 
(1, 'Luis Martín', '675678901', 'luis.martin@peixos.com', 1),
(2, 'Marta Jiménez', '676789012', 'marta.jimenez@carns.com', 2),
(3, 'Pedro Díaz', '677890123', 'pedro.diaz@gelats.com', 3),
(4, 'Antonio Ruiz', '678901234', 'antonio.ruiz@preparats.com', 4);

-- Insertar Relaciones Direcciones Particulares
INSERT INTO R_ADRECA_PARTICULAR (idParticular, idAdreca) 
VALUES 
('F12345678', 1),
('G23456789', 2),
('H34567890', 3),
('I45678901', 4),
('J56789012', 5);
