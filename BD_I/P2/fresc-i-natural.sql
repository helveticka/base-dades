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
(4, 'Plats preparats'), 
(5, 'Verdures'), 
(6, 'Marisc'), 
(7, 'Fruta'), 
(8, 'Làctics'), 
(9, 'Pastissos'), 
(10, 'Bebidas'), 
(11, 'Cereals'), 
(12, 'Sopes'), 
(13, 'Condiments'), 
(14, 'Pa'), 
(15, 'Fruits secs'); 
 
-- Insertar Zones 
INSERT INTO ZONA (idZona, nomZona, idIlla)  
VALUES  
(1, 'Zona Nord', 1), 
(2, 'Zona Sud', 1), 
(3, 'Zona Est', 2), 
(4, 'Zona Oest', 3), 
(5, 'Zona Centre', 1), 
(6, 'Zona Eivissa', 3), 
(7, 'Zona Formentera', 4), 
(8, 'Zona Costa', 1), 
(9, 'Zona Dins', 2), 
(10, 'Zona Alta', 1), 
(11, 'Zona Baixa', 3), 
(12, 'Zona Interior', 2), 
(13, 'Zona Marítima', 4), 
(14, 'Zona de Montaña', 1), 
(15, 'Zona Exterior', 3); 
 
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
(10), 
(11), 
(12), 
(13), 
(14), 
(15); 
 
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
(10, 'Carrer Sant Llorenç', 8, 'Piso 2', 10), 
(11, 'Carrer Nou', 30, 'Piso 1', 11), 
(12, 'Carrer Lluna', 12, 'Piso 3', 12), 
(13, 'Carrer del Sol', 100, 'Piso 5', 13), 
(14, 'Carrer Pau', 40, 'Piso 6', 14), 
(15, 'Carrer Gran Via', 22, 'Piso 7', 15); 
 
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
(10, 'Magatzem Sant Llorenç', 450, 10), 
(11, 'Magatzem Formentera', 300, 11), 
(12, 'Magatzem Ciutat Vella', 1000, 12), 
(13, 'Magatzem Ses Salines', 1500, 13), 
(14, 'Magatzem Es Vedrà', 650, 14), 
(15, 'Magatzem Porto Cristo', 950, 15); 
 
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
(10, 'Sant Llorenç', 85.15, 12000, 1, 10), 
(11, 'Formentera', 84.32, 12000, 4, 11), 
(12, 'Ciutat Vella', 200.00, 50000, 4, 12), 
(13, 'Es Vedrà', 20.00, 2000, 5, 13), 
(14, 'Es Bosc', 150.00, 30000, 6, 14), 
(15, 'S’Illot', 10.00, 5000, 7, 15); 
 
-- Insertar Proveïdors 
INSERT INTO PROVEIDOR (idProveidor, nomProveidor, raoSocial, cif, tempsLliurament, idAdrecaFiscal, idAdrecaDistribucio)  
VALUES  
(1, 'Peixos del Nord S.L.', 'Peixos del Nord', 'A12345678', 5, 1, 2), 
(2, 'Carns Puro S.A.', 'Carns Puro', 'B23456789', 3, 3, 4), 
(3, 'Gelats Calents', 'Gelats Calents S.L.', 'C34567890', 7, 5, 6), 
(4, 'Preparats Gourmet', 'Preparats Gourmet', 'D45678901', 10, 7, 8), 
(5, 'Sushi Barcelona', 'Sushi S.L.', 'E56789012', 6, 9, 10), 
(6, 'Bebidas del Mundo', 'Bebidas del Mundo S.A.', 'F67890123', 2, 11, 12), 
(7, 'Frutas y Verduras', 'Frutas y Verduras S.L.', 'G78901234', 3, 13, 14), 
(8, 'Vinos de Mallorca', 'Vinos Mallorca', 'H89012345', 4, 15, 1), 
(9, 'Productos Lácteos', 'Lácteos Mallorca', 'I90123456', 6, 2, 3), 
(10, 'Aceitunas La Verde', 'Aceitunas La Verde S.A.', 'J01234567', 5, 4, 5), 
(11, 'Carnes del Valle', 'Carnes del Valle S.L.', 'K12345678', 7, 6, 7), 
(12, 'Pastelería del Sol', 'Pastelería Sol', 'L23456789', 10, 8, 9), 
(13, 'Delicias Mediterráneas', 'Delicias Mediterráneas S.L.', 'M34567890', 2, 10, 11), 
(14, 'Mariscos y más', 'Mariscos S.L.', 'N45678901', 3, 12, 13), 
(15, 'La Huerta del Sol', 'La Huerta S.L.', 'O56789012', 4, 14, 15); 
 
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
(110, 'Filets de Peix', 4.19, 'Filets de peix blanc ultracongelat', 'kg', 'Caixa', 1, 1), 
(111, 'Sushi de Salmó', 8.99, 'Sushi de salmó fresc', 'Unitat', 'Unitat', 5, 5), 
(112, 'Sushi Vegetal', 7.49, 'Sushi amb verdures', 'Unitat', 'Unitat', 5, 5), 
(113, 'Llet de Mallorca', 1.79, 'Llet fresca', 'L', 'Caixa', 8, 9), 
(114, 'Patates Fregides', 1.99, 'Patates fregides de la millor qualitat', 'kg', 'Unitat', 7, 7), 
(115, 'Pa de Mallorca', 3.49, 'Pa artesà de Mallorca', 'Unitat', 'Unitat', 15, 14); 
 
-- Insertar Lots 
INSERT INTO LOT (numeroLot, quantitat, caducitat, idMagatzem, idProducte, idProveidor)  
VALUES  
(1, 90, '2025-12-31', 1, 101, 1), 
(2, 50, '2025-06-03', 2, 102, 2), 
(3, 80, '2025-05-30', 3, 103, 3), 
(4, 200, '2025-07-10', 4, 104, 4), 
(5, 250, '2025-12-01', 5, 105, 1), 
(6, 20, '2025-11-20', 6, 106, 1), 
(7, 140, '2025-08-30', 7, 107, 2), 
(8, 110, '2025-09-15', 8, 108, 3), 
(9, 130, '2025-10-10', 9, 109, 4), 
(10, 100, '2025-12-15', 10, 110, 1), 
(11, 200, '2025-09-05', 11, 111, 5), 
(12, 250, '2025-06-13', 12, 112, 5), 
(13, 150, '2025-11-30', 13, 113, 9), 
(14, 88, '2025-12-01', 14, 114, 7), 
(15, 200, '2025-12-20', 15, 115, 14); 
 
-- Insertar Personas Responsables 
INSERT INTO PERSONA_RESPONSABLE (idResponsable, càrrec, dataInici, dataFi, nomResponsable, telefonResponsable, emailResponsable, idMagatzem)   
VALUES   
(1, 'Gerent', '2024-01-01', NULL, 'Maria López', '600123456', 'maria.lopez@fresc.com', 1),  
(2, 'Encargat', '2023-05-01', '2024-05-01', 'Antonio Fernández', '601234567', 'antonio.fernandez@fresc.com', 2),  
(3, 'Supervisor', '2023-10-01', NULL, 'Pere Ruiz', '602345678', 'pere.ruiz@fresc.com', 3),  
(4, 'Director', '2022-06-15', NULL, 'Carla Díaz', '603456789', 'carla.diaz@fresc.com', 4),  
(5, 'Jefe de logística', '2021-03-10', NULL, 'Sergio González', '604567890', 'sergio.gonzalez@fresc.com', 5),  
(6, 'Responsable de ventas', '2020-05-12', '2023-04-20', 'Laura Martínez', '605678901', 'laura.martinez@fresc.com', 6),  
(7, 'Operativo', '2024-01-01', NULL, 'David Ruiz', '606789012', 'david.ruiz@fresc.com', 7),  
(8, 'Supervisor de stock', '2023-11-01', NULL, 'José Pérez', '607890123', 'jose.perez@fresc.com', 8), 
(9,'Gerent','2023-01-01','2023-12-31','Anna Vidal','610111111','anna.vidal@fresc.com',1), 
(10,'Supervisor','2022-08-01','2023-09-30','Joan Miquel','610222222','joan.miquel@fresc.com',3), 
(11,'Director','2021-04-01','2022-06-14','Marta Soler','610333333','marta.soler@fresc.com',4), 
(12,'Jefe de logística','2019-12-01','2021-03-09','Ignasi Pujol','610444444','ignasi.pujol@fresc.com',5), 
(13,'Operativo','2023-01-01','2023-12-31','Cristina Alarcón','610555555','cristina.alarcon@fresc.com',7),
(16, 'Gerent', '2025-05-13', NULL, 'Jordi Pons', '611000003', 'jordi.pons@fresc.com', 9),
(17, 'Encargat', '2025-05-13', NULL, 'Laura Serra', '611000004', 'laura.serra@fresc.com', 10),
(18, 'Director', '2025-05-13', NULL, 'Carlos Riera', '611000005', 'carlos.riera@fresc.com', 11),
(19, 'Responsable de logística', '2025-05-13', NULL, 'Eva Bonet', '611000006', 'eva.bonet@fresc.com', 12),
(20, 'Supervisor', '2025-05-13', NULL, 'Xavier Nadal', '611000007', 'xavier.nadal@fresc.com', 13),
(21, 'Gerent', '2025-05-13', NULL, 'Clara Bosch', '611000008', 'clara.bosch@fresc.com', 14),
(22, 'Jefe de almacén', '2025-05-13', NULL, 'Toni Ferrer', '611000009', 'toni.ferrer@fresc.com', 15);

-- Insertar Vehículos 
INSERT INTO VEHICLE (matricula, marca, model, capacitat, idMagatzem)  
VALUES  
('IB1234AB', 'Mercedes', 'Sprinter', 1000, 1), 
('MA2345CD', 'Renault', 'Master', 800, 2), 
('ME3456EF', 'Fiat', 'Ducato', 950, 3), 
('EV4567GH', 'Ford', 'Transit', 1200, 4), 
('SA5678IJ', 'Peugeot', 'Boxer', 1000, 5), 
('MU6789KL', 'Mercedes', 'Vito', 750, 6), 
('CI7890MN', 'Volkswagen', 'Crafter', 1100, 7), 
('SA8901OP', 'Fiat', 'Ducato', 1000, 8), 
('CI9012QR', 'Ford', 'Transit', 900, 9), 
('ME0123ST', 'Renault', 'Master', 950, 10), 
('EV1234TU', 'Mercedes', 'Sprinter', 1050, 11), 
('MU2345UV', 'Peugeot', 'Boxer', 850, 12), 
('SA3456WX', 'Fiat', 'Ducato', 1200, 13), 
('CI4567YZ', 'Volkswagen', 'Crafter', 1300, 14), 
('ME5678AB', 'Renault', 'Master', 1400, 15); 
 
-- Insertar Càrregues 
INSERT INTO CÀRREGA (idCarrega, jornada, idVehicle)  
VALUES  
(1, '2025-01-15', 'IB1234AB'), 
(2, '2025-02-10', 'MA2345CD'), 
(3, '2025-03-05', 'ME3456EF'), 
(4, '2025-04-12', 'EV4567GH'), 
(5, '2025-05-20', 'SA5678IJ'), 
(6, '2025-06-05', 'MU6789KL'), 
(7, '2025-07-15', 'CI7890MN'), 
(8, '2025-08-01', 'SA8901OP'), 
(9, '2025-09-10', 'CI9012QR'), 
(10, '2025-10-20', 'ME0123ST'),
(11, '2025-11-01', 'EV1234TU'), 
(12, '2025-12-10', 'MU2345UV'), 
(13, '2026-01-25', 'SA3456WX'), 
(14, '2026-02-15', 'CI4567YZ'), 
(15, '2026-03-01', 'ME5678AB'); 
 
-- Insertar Quantitat Càrrega 
INSERT INTO QUANTITAT_CÀRREGA (idQCarrega, unitats, idCarrega, numLot)  
VALUES  
(1, 100, 1, 1), 
(2, 150, 2, 2), 
(3, 80, 3, 3), 
(4, 200, 4, 4), 
(5, 250, 5, 5), 
(6, 120, 6, 6), 
(7, 90, 7, 1), 
(8, 180, 8, 2), 
(9, 150, 9, 3), 
(10, 200, 10, 4), 
(11, 250, 11, 5), 
(12, 300, 12, 6), 
(13, 200, 13, 7), 
(14, 150, 14, 8), 
(15, 180, 15, 9); 
 
-- Insertar Venedors 
INSERT INTO VENEDOR (idVenedor, nomVenedor, telefonVenedor, emailVenedor, idVehicle, idMagatzem)  
VALUES  
(1, 'Joan Garcia', '600123456', 'joan.garcia@fresc.com', 'IB1234AB', 1), 
(2, 'Pere Ruiz', '601234567', 'pere.ruiz@fresc.com', 'MA2345CD', 2), 
(3, 'Maria López', '602345678', 'maria.lopez@fresc.com', 'ME3456EF', 3), 
(4, 'Antonio Fernández', '603456789', 'antonio.fernandez@fresc.com', 'EV4567GH', 4), 
(5, 'Carla Sánchez', '604567890', 'carla.sanchez@fresc.com', 'SA5678IJ', 5), 
(6, 'David López', '605678901', 'david.lopez@fresc.com', 'MU6789KL', 6), 
(7, 'Laura Martínez', '606789012', 'laura.martinez@fresc.com', 'CI7890MN', 7), 
(8, 'José Pérez', '607890123', 'jose.perez@fresc.com', 'SA8901OP', 8), 
(9, 'Elena García', '608901234', 'elena.garcia@fresc.com', 'CI9012QR', 9), 
(10, 'Miguel Fernández', '609012345', 'miguel.fernandez@fresc.com', 'ME0123ST', 10), 
(11, 'Luis Martín', '610123456', 'luis.martin@fresc.com', 'EV1234TU', 11), 
(12, 'Patricia Sánchez', '611234567', 'patricia.sanchez@fresc.com', 'MU2345UV', 12), 
(13, 'Carlos González', '612345678', 'carlos.gonzalez@fresc.com', 'SA3456WX', 13), 
(14, 'Javier Ruiz', '613456789', 'javier.ruiz@fresc.com', 'CI4567YZ', 14), 
(15, 'Isabel López', '614567890', 'isabel.lopez@fresc.com', 'ME5678AB', 15); 
 
-- Insertar Vendes 
INSERT INTO VENDA (idVenda, data, idVenedor, idClient)  
VALUES  
(1, '2025-01-10', 1, 'P12345678'), 
(2, '2025-02-15', 2, 'P23456789'), 
(3, '2024-03-20', 3, 'P23456789'), 
(4, '2025-04-25', 4, 'P56789012'), 
(5, '2024-05-05', 5, 'P56789012'), 
(6, '2024-06-15', 6, 'P67890123'), 
(7, '2024-07-01', 7, 'P78901234'), 
(8, '2024-07-20', 8, 'P89012345'), 
(9, '2024-08-05', 9, 'P90123456'), 
(10, '2024-08-20', 10, 'P01234567'), 
(11, '2024-09-05', 11, 'P12345679'), 
(12, '2024-09-15', 12, 'P23456780'), 
(13, '2024-10-01', 13, 'P34567891'), 
(14, '2024-10-10', 14, 'P45678902'), 
(15, '2024-10-25', 15, 'P56789013'),
(16, '2025-05-01', 3, 'A12345678'),
(17, '2025-05-02', 5, 'B23456789'),
(18, '2025-05-03', 1, 'C34567890'),
(19, '2025-05-04', 7, 'D45678901'),
(20, '2025-05-05', 2, 'E56789012'),
(21, '2025-05-06', 4, 'F67890123');
 
-- Insertar Quantitat_Venúda 
INSERT INTO QUANTITAT_VENUDA (idVenda, idQCarrega, unitats)  
VALUES  
(1, 1, 10), 
(2, 2, 15), 
(3, 3, 8), 
(4, 4, 20), 
(5, 5, 12), 
(6, 6, 18), 
(7, 7, 14), 
(8, 8, 25), 
(9, 9, 18), 
(10, 10, 30), 
(11, 11, 10), 
(12, 12, 15), 
(13, 13, 20), 
(14, 14, 25), 
(15, 15, 30),
(16, 1, 20),
(17, 2, 30),
(18, 3, 15),
(19, 4, 25),
(20, 5, 40),
(21, 6, 35);
 
-- Insertar Clientes Particulares 
INSERT INTO PARTICULAR (idFiscal, nomParticular, telefonParticular, emailParticular)  
VALUES  
('P12345678', 'Juan Pérez', '650123456', 'juan.perez@gmail.com'), 
('P23456789', 'Ana Martínez', '651234567', 'ana.martinez@yahoo.com'), 
('P34567890', 'Carlos Gómez', '652345678', 'carlos.gomez@hotmail.com'),
('P45678901', 'Lucía Fernández', '653456789', 'lucia.fernandez@gmail.com'), 
('P56789012', 'David López', '654567890', 'david.lopez@yahoo.com'), 
('P67890123', 'Eva Ruiz', '655678901', 'eva.ruiz@gmail.com'), 
('P78901234', 'Ricardo Fernández', '656789012', 'ricardo.fernandez@yahoo.com'), 
('P89012345', 'Sandra Martínez', '657890123', 'sandra.martinez@hotmail.com'), 
('P90123456', 'José Rodríguez', '658901234', 'jose.rodriguez@yahoo.com'), 
('P01234567', 'Antonio Sánchez', '659012345', 'antonio.sanchez@hotmail.com'), 
('P12345679', 'María López', '660123456', 'maria.lopez@gmail.com'), 
('P23456780', 'Juan González', '661234567', 'juan.gonzalez@yahoo.com'), 
('P34567891', 'Esteban Castro', '662345678', 'esteban.castro@gmail.com'), 
('P45678902', 'Marta García', '663456789', 'marta.garcia@yahoo.com'), 
('P56789013', 'Beatriz Pérez', '664567890', 'beatriz.perez@hotmail.com'); 
 
-- Insertar Empresas 
INSERT INTO EMPRESA (idFiscal, nomEmpresa, terminiPagament, raoSocial, idAdreca)  
VALUES  
('A12345678', 'Restaurante Can X', 30, 'Restaurante Can X S.L.', 1), 
('B23456789', 'Hotel La Palma', 60, 'Hotel La Palma S.A.', 2), 
('C34567890', 'Catering Isla Azul', 90, 'Catering Isla Azul S.L.', 3), 
('D45678901', 'Restaurante Mar y Sol', 30, 'Restaurante Mar y Sol S.L.', 4), 
('E56789012', 'Hotel Ibiza Dream', 60, 'Hotel Ibiza Dream S.A.', 5), 
('F67890123', 'Catering Mediterráneo', 30, 'Catering Mediterráneo S.L.', 6), 
('G78901234', 'Hotel Sol Mar', 60, 'Hotel Sol Mar S.A.', 7), 
('H89012345', 'Restaurante El Faro', 30, 'Restaurante El Faro S.L.', 8), 
('I90123456', 'Catering Mediterráneo', 60, 'Catering Mediterráneo S.L.', 9), 
('J01234567', 'Hotel Costa Azul', 90, 'Hotel Costa Azul S.A.', 10), 
('K12345678', 'Restaurante La Isla', 30, 'Restaurante La Isla S.L.', 11), 
('L23456789', 'Hotel La Mare', 60, 'Hotel La Mare S.A.', 12), 
('M34567890', 'Restaurante Paladar', 30, 'Restaurante Paladar S.L.', 13), 
('N45678901', 'Hotel El Mar', 90, 'Hotel El Mar S.A.', 14), 
('O56789012', 'Catering La Terra', 30, 'Catering La Terra S.L.', 15); 
 
-- Insertar Persona Contacte 
INSERT INTO PERSONA_CONTACTE (idPersonaContacte, idEmpresa, nomPersonaContacte, telefonPersonaContacte, emailPersonaContacte)  
VALUES  
(1, 'A12345678', 'Carlos Sánchez', '670123456', 'carlos.sanchez@restx.com'), 
(2, 'B23456789', 'Laura Pérez', '671234567', 'laura.perez@hotelpalma.com'), 
(3, 'C34567890', 'Sergio López', '672345678', 'sergio.lopez@cateringisla.com'), 
(4, 'D45678901', 'Raquel Gómez', '673456789', 'raquel.gomez@restmar.com'), 
(5, 'E56789012', 'José Martínez', '674567890', 'jose.martinez@ibizadream.com'), 
(6, 'F67890123', 'Pablo Díaz', '675678901', 'pablo.diaz@cateringmed.com'), 
(7, 'G78901234', 'Marta Ruiz', '676789012', 'marta.ruiz@hotelsolmar.com'), 
(8, 'H89012345', 'Ricardo Sánchez', '677890123', 'ricardo.sanchez@restaurantefaro.com'), 
(9, 'I90123456', 'Cristina Fernández', '678901234', 'cristina.fernandez@cateringterra.com'), 
(10, 'J01234567', 'Luis Martínez', '679012345', 'luis.martinez@hotelmar.com'), 
(11, 'K12345678', 'Felipe Ruiz', '680123456', 'felipe.ruiz@restpaladar.com'), 
(12, 'L23456789', 'Julia Gómez', '681234567', 'julia.gomez@hotelmare.com'), 
(13, 'M34567890', 'Fernando Castro', '682345678', 'fernando.castro@hotelcostazul.com'), 
(14, 'N45678901', 'Javier Rodríguez', '683456789', 'javier.rodriguez@restislacanarias.com'), 
(15, 'O56789012', 'Carmen Sánchez', '684567890', 'carmen.sanchez@cateringterranostra.com'); 




-- Insertar Persona Referencia 
INSERT INTO PERSONA_REFERENCIA (idPersonaReferencia, nomPersonaReferencia, telefonPersonaReferencia, emailPersonaReferencia, idProveidor)  
VALUES  
(1, 'Luis Martín', '675678901', 'luis.martin@peixos.com', 1), 
(2, 'Marta Jiménez', '676789012', 'marta.jimenez@carns.com', 2), 
(3, 'Pedro Díaz', '677890123', 'pedro.diaz@gelats.com', 3), 
(4, 'Antonio Ruiz', '678901234', 'antonio.ruiz@preparats.com', 4), 
(5, 'Luis Fernández', '679012345', 'luis.fernandez@productoslacteos.com', 9), 
(6, 'David Sánchez', '680123456', 'david.sanchez@mariscosymas.com', 14), 
(7, 'Fernando Pérez', '681234567', 'fernando.perez@vinosmallorca.com', 8), 
(8, 'Isabel Martín', '682345678', 'isabel.martin@pasteleriasol.com', 12), 
(9, 'Carlos Rodríguez', '683456789', 'carlos.rodriguez@cerealesmallorca.com', 11), 
(10, 'José Gómez', '684567890', 'jose.gomez@deliciasmediterraneas.com', 13), 
(11, 'Laura Pérez', '685678901', 'laura.perez@bebidasdalmundo.com', 6), 
(12, 'Ricardo López', '686789012', 'ricardo.lopez@frutasyverduras.com', 7), 
(13, 'Sergio González', '687890123', 'sergio.gonzalez@aceitunaslaverde.com', 10), 
(14, 'Marta Castro', '688901234', 'marta.castro@productoslacteos.com', 9), 
(15, 'Pablo Martínez', '689012345', 'pablo.martinez@peixosdelnord.com', 1); 

-- Insertar Relaciones Direcciones Particulares 
INSERT INTO R_ADRECA_PARTICULAR (idParticular, idAdreca)  
VALUES  
('P12345678', 1), 
('P23456789', 2), 
('P34567890', 3), 
('P45678901', 4), 
('P56789012', 5), 
('P67890123', 6), 
('P78901234', 7), 
('P89012345', 8), 
('P90123456', 9), 
('P01234567', 10), 
('P12345679', 11), 
('P23456780', 12), 
('P34567891', 13), 
('P45678902', 14), 
('P56789013', 15);
