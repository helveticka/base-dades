-- Taula COMPNAYIA
CREATE TABLE COMPANYIA (
    nom_companyia VARCHAR(128) PRIMARY KEY,
    disciplina_artistica VARCHAR(128)
);

-- Taula ESPECTACLE
CREATE TABLE ESPECTACLE (
    nom_espectacle VARCHAR(128) PRIMARY KEY,
    descripció TEXT,
    durada TIME,
    públic VARCHAR(128),
    classificació_edat TINYINT,
    gènere ENUM('Comèdia', 'Drama', 'Musical', 'Òpera')
);

-- Taula ESTAT_SESSIÓ
CREATE TABLE ESTAT_SESSIÓ (
    estat_sessió ENUM('Programada', 'Cancel·lada', 'Finalitzada', 'En curs') PRIMARY KEY
);

-- Taula IDIOMA
CREATE TABLE IDIOMA (
    idioma VARCHAR(32) PRIMARY KEY
);

-- Taula CIUTAT
CREATE TABLE CIUTAT (
    ciutat VARCHAR(64) PRIMARY KEY,
    país VARCHAR(64)
);

-- Taula TIPUS
CREATE TABLE TIPUS_SEIENT (
    tipus_seient VARCHAR(64) PRIMARY KEY,
    preu_seient DECIMAL(8,2)
);

-- Taula ESTAT_ENTRADA
CREATE TABLE ESTAT_ENTRADA (
    estat_entrada ENUM('Activa', 'Cancel·lada', 'Utilitzada') PRIMARY KEY
);

-- Taula DESCOMPTE
CREATE TABLE DESCOMPTE (
    tipus_descompte ENUM('Estudiants', 'Jubilats', 'Grups') PRIMARY KEY,
    percentatge DECIMAL(5,2)
);

-- Taula PERSONA
CREATE TABLE PERSONA (
    id_persona INT PRIMARY KEY,
    nom VARCHAR(64),
    llinatges VARCHAR(128),
    telèfon VARCHAR(16)
);

-- Taula LLOC
CREATE TABLE LLOC (
    nom_lloc VARCHAR(128) PRIMARY KEY,
    tipus_lloc VARCHAR(64),
    ubicació VARCHAR(128),
    capacitat INT,
    ciutat VARCHAR(64),
    FOREIGN KEY (ciutat) REFERENCES CIUTAT(ciutat)
);

-- Taula SESSIÓ
CREATE TABLE SESSIÓ (
    id_sessió INT PRIMARY KEY,
    preu_base DECIMAL(8,2),
    data DATE,
    hora TIME,
    nom_espectacle VARCHAR(128),
    estat_sessió ENUM('Programada', 'Cancel·lada', 'Finalitzada', 'En curs'),
    idioma_subtitol VARCHAR(32),
    idioma_original VARCHAR(32),
    idioma_traducció VARCHAR(32),
    nom_lloc VARCHAR(128),
    FOREIGN KEY (nom_espectacle) REFERENCES ESPECTACLE(nom_espectacle),
    FOREIGN KEY (estat_sessió) REFERENCES ESTAT_SESSIÓ(estat_sessió),
    FOREIGN KEY (idioma_subtitol) REFERENCES IDIOMA(idioma),
    FOREIGN KEY (idioma_original) REFERENCES IDIOMA(idioma),
    FOREIGN KEY (idioma_traducció) REFERENCES IDIOMA(idioma),
    FOREIGN KEY (nom_lloc) REFERENCES LLOC(nom_lloc)
);

-- Taula ZONA
CREATE TABLE ZONA (
    id_zona INT PRIMARY KEY,
    preu_zona DECIMAL(8,2),
    numeració BOOLEAN,
    ascensor BOOLEAN,
    rampa BOOLEAN,
    mobilitat_reduïda BOOLEAN,
    nom_lloc VARCHAR(128),
    FOREIGN KEY (nom_lloc) REFERENCES LLOC(nom_lloc)
);

-- Taula SEIENT
CREATE TABLE SEIENT (
    número INT PRIMARY KEY,
    tipus_seient VARCHAR(64),
    id_zona INT,
    FOREIGN KEY (tipus_seient) REFERENCES TIPUS_SEIENT(tipus_seient),
    FOREIGN KEY (id_zona) REFERENCES ZONA(id_zona)
);

-- Taula EMPRESA
CREATE TABLE EMPRESA (
    nom_empresa VARCHAR(128) PRIMARY KEY,
    tipus_servei VARCHAR(128),
    telèfon VARCHAR(16),
    email VARCHAR(128),
    persona_referència VARCHAR(128)
);

-- Taula PERSONAL
CREATE TABLE PERSONAL (
    id_personal INT PRIMARY KEY,
    mail VARCHAR(128),
    tipus VARCHAR(64),
    rol_especific VARCHAR(128),
    nom_empresa VARCHAR(128),
    FOREIGN KEY (id_personal) REFERENCES PERSONA(id_persona),
    FOREIGN KEY (nom_empresa) REFERENCES EMPRESA(nom_empresa)
);

-- Taula ARTISTA
CREATE TABLE ARTISTA (
    id_artista INT PRIMARY KEY,
    especialitat_artistica VARCHAR(128),
    nom_companyia VARCHAR(128),
    FOREIGN KEY (id_artista) REFERENCES PERSONA(id_persona),
    FOREIGN KEY (nom_companyia) REFERENCES COMPANYIA(nom_companyia)
);

-- Taula CLIENT
CREATE TABLE CLIENT (
    id_client INT PRIMARY KEY,
    email VARCHAR(128),
    tipus_descompte ENUM('Estudiants', 'Jubilats', 'Grups'),
    FOREIGN KEY (id_client) REFERENCES PERSONA(id_persona),
    FOREIGN KEY (tipus_descompte) REFERENCES DESCOMPTE(tipus_descompte)
);

-- Taula COMPRA
CREATE TABLE COMPRA (
    id_compra INT PRIMARY KEY,
    data DATE,
    hora TIME,
    assegurança BOOLEAN,
    vip BOOLEAN,
    id_client INT,
    tipus_descompte ENUM('Estudiants', 'Jubilats', 'Grups'),
    FOREIGN KEY (id_client) REFERENCES CLIENT(id_client),
    FOREIGN KEY (tipus_descompte) REFERENCES DESCOMPTE(tipus_descompte)
);

-- Taula ENTRADA
CREATE TABLE ENTRADA (
    id_entrada INT PRIMARY KEY,
    preu_entrada DECIMAL(8,2),
    id_compra INT,
    id_sessió INT,
    estat_entrada ENUM('Activa', 'Cancel·lada', 'Utilitzada'),
    número INT,
    id_zona INT,
    FOREIGN KEY (id_compra) REFERENCES COMPRA(id_compra),
    FOREIGN KEY (id_sessió) REFERENCES SESSIÓ(id_sessió),
    FOREIGN KEY (estat_entrada) REFERENCES ESTAT_ENTRADA(estat_entrada),
    FOREIGN KEY (número) REFERENCES SEIENT(número),
    FOREIGN KEY (id_zona) REFERENCES ZONA(id_zona)
);

-- Taula CÀRREC
CREATE TABLE CARREC (
    id_sessió INT,
    id_personal INT,
    rol VARCHAR(128),
    PRIMARY KEY (id_sessió, id_personal),
    FOREIGN KEY (id_sessió) REFERENCES SESSIÓ(id_sessió),
    FOREIGN KEY (id_personal) REFERENCES PERSONAL(id_personal)
);

-- Taula PAPER
CREATE TABLE PAPER (
    id_artista INT,
    nom_espectacle VARCHAR(128),
    paper VARCHAR(128),
    PRIMARY KEY (id_artista, nom_espectacle),
    FOREIGN KEY (id_artista) REFERENCES ARTISTA(id_artista),
    FOREIGN KEY (nom_espectacle) REFERENCES ESPECTACLE(nom_espectacle)
);