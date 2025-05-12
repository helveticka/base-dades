-- Crear la base de dades
CREATE DATABASE FrescINatural;
USE FrescINatural;

-- Crear la taula ILLA
CREATE TABLE ILLA (
    idIlla INT PRIMARY KEY,
    nom_illa VARCHAR(64) NOT NULL
);

-- Crear la taula CLIENT
CREATE TABLE CLIENT (
    idClient INT PRIMARY KEY,
    subministrament VARCHAR(128) NOT NULL
);

-- Crear la taula CATEGORIA
CREATE TABLE CATEGORIA (
    idCategoria INT PRIMARY KEY,
    categoria VARCHAR(64) NOT NULL
);

-- Crear la taula QUANTITAT_VENUDA
CREATE TABLE QUANTITAT_VENUDA (
    idQVenuda INT PRIMARY KEY,
    unitats INT NOT NULL
);

-- Crear la taula ZONA
CREATE TABLE ZONA (
    idZona INT PRIMARY KEY,
    nomZona VARCHAR(64) NOT NULL,
    idIlla INT NOT NULL,
    CONSTRAINT fk_zona_illa FOREIGN KEY (idIlla) REFERENCES ILLA(idIlla)
);

-- Crear la taula ADRECA
CREATE TABLE ADRECA (
    idAdreca INT PRIMARY KEY,
    nomAdreca VARCHAR(128) NOT NULL,
    idMunicipi INT NOT NULL
);

-- Crear la taula MAGATZEM
CREATE TABLE MAGATZEM (
    idMagatzem INT PRIMARY KEY,
    nom VARCHAR(128) NOT NULL,
    capacitat INT NOT NULL,
    idAdreca INT NOT NULL,
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
    idAdrecaFiscal INT NOT NULL,
    idAdrecaDistribucio INT NOT NULL,
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
    CONSTRAINT fk_lot_magatzem FOREIGN KEY (idMagatzem) REFERENCES MAGATZEM(idMagatzem),
    CONSTRAINT fk_lot_producte FOREIGN KEY (idProducte) REFERENCES PRODUCTE(referencia)
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
    idClient INT NOT NULL,
    idVehicle VARCHAR(16) NOT NULL,
    idMagatzem INT NOT NULL,
    CONSTRAINT fk_venedor_client FOREIGN KEY (idClient) REFERENCES CLIENT(idClient),
    CONSTRAINT fk_venedor_vehicle FOREIGN KEY (idVehicle) REFERENCES VEHICLE(matricula),
    CONSTRAINT fk_venedor_magatzem FOREIGN KEY (idMagatzem) REFERENCES MAGATZEM(idMagatzem)
);

-- Crear la taula VENDA
CREATE TABLE VENDA (
    idVenda INT PRIMARY KEY,
    data DATE NOT NULL,
    idVenedor INT NOT NULL,
    CONSTRAINT fk_venda_venedor FOREIGN KEY (idVenedor) REFERENCES VENEDOR(idVenedor)
);

-- Crear la taula PARTICULAR
CREATE TABLE PARTICULAR (
    DNI VARCHAR(16) PRIMARY KEY,
    nomParticular VARCHAR(128) NOT NULL,
    telefonParticular VARCHAR(32) NOT NULL,
    emailParticular VARCHAR(128) NOT NULL,
    subministrament VARCHAR(128) NOT NULL,
    idAdrecaPrincipal INT NOT NULL,
    CONSTRAINT fk_particular_adrecaPrincipal FOREIGN KEY (idAdrecaPrincipal) REFERENCES ADRECA(idAdreca)
);

-- Crear la taula EMPRESA
CREATE TABLE EMPRESA (
    CIF VARCHAR(16) PRIMARY KEY,
    nom VARCHAR(128) NOT NULL,
    terminiPagament INT NOT NULL,
    raoSocial VARCHAR(128),
    subministrament VARCHAR(128),
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
    CONSTRAINT fk_personaContacte_empresa FOREIGN KEY (idEmpresa) REFERENCES EMPRESA(CIF)
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


-- Crear la taula R_CATEGORIA_PROVEIDOR
CREATE TABLE R_CATEGORIA_PROVEIDOR (
    idCategoria INT,
    idProveidor INT,
    PRIMARY KEY (idCategoria, idProveidor),
    CONSTRAINT fk_r_categoria_proveidor_categoria FOREIGN KEY (idCategoria) REFERENCES CATEGORIA(idCategoria),
    CONSTRAINT fk_r_categoria_proveidor_proveidor FOREIGN KEY (idProveidor) REFERENCES PROVEIDOR(idProveidor)
);

-- Crear la taula R_VENDA_QUANTITAT_CARREGA
CREATE TABLE R_VENDA_QUANTITAT_CARREGA (
    idVenda INT,
    idQCarrega INT,
    PRIMARY KEY (idVenda, idQCarrega),
    CONSTRAINT fk_r_venda_quantitatCarrega_venda FOREIGN KEY (idVenda) REFERENCES VENDA(idVenda),
    CONSTRAINT fk_r_venda_quantitatCarrega_carrega FOREIGN KEY (idQCarrega) REFERENCES QUANTITAT_CÀRREGA(idQCarrega)
);

-- Crear la taula ADRECES_ADICIONALS_PARTICULAR
CREATE TABLE ADRECES_ADICIONALS_PARTICULAR (
    idParticular VARCHAR(16),
    idAdreca INT,
    PRIMARY KEY (idParticular, idAdreca),
    CONSTRAINT fk_adrecesAdicionals_particular FOREIGN KEY (idParticular) REFERENCES PARTICULAR(DNI),
    CONSTRAINT fk_adrecesAdicionals_adreca FOREIGN KEY (idAdreca) REFERENCES ADRECA(idAdreca)
);

ALTER TABLE ADRECA ADD CONSTRAINT fk_adreca_municipi FOREIGN KEY (idMunicipi) REFERENCES MUNICIPI(idMunicipi);
