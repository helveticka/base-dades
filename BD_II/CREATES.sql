CREATE DATABASE BD2SQLITO;

USE BD2SQLITO;

-- Tablas de backup (no relacionadas)
CREATE TABLE Ayuntamiento_backup (
    id INT,
    nombre VARCHAR(100),
    id_provincia INT
);

CREATE TABLE Colonia_backup (
    id INT,
    latitud DECIMAL(10,8),
    longitud DECIMAL(11,8),
    descripción TEXT,
    id_ayuntamiento INT
);

CREATE TABLE Gato_backup (
    id INT,
    num_chip VARCHAR(50),
    descripción TEXT,
    foto VARCHAR(255),
    id_colonia INT
);

-- Tabla de log de backups
CREATE TABLE Backup_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATETIME NOT NULL,
    exito BOOL NOT NULL,
    mensaje TEXT
);

-- País (País) 
CREATE TABLE Pais ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(100) NOT NULL UNIQUE 
); 

-- Comunidad_autonoma (Comunidad Autónoma) 
CREATE TABLE Comunidad_autonoma ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(100) NOT NULL UNIQUE, 
    id_pais INT NOT NULL, 
    CONSTRAINT fk_comunidad_autonoma_pais FOREIGN KEY (id_pais) REFERENCES Pais(id) 
); 

-- Provincia (Provincia) 
CREATE TABLE Provincia ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(100) NOT NULL UNIQUE, 
    id_comunidad_autonoma INT NOT NULL, 
    CONSTRAINT fk_provincia_comunidad_autonoma FOREIGN KEY (id_comunidad_autonoma) REFERENCES Comunidad_autonoma(id) 
); 

-- Ayuntamiento (Ayuntamiento) 
CREATE TABLE Ayuntamiento ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(100) NOT NULL, 
    id_provincia INT NOT NULL, 
    CONSTRAINT fk_ayuntamiento_provincia FOREIGN KEY (id_provincia) REFERENCES Provincia(id) 
); 

-- Rol (Rol) 
CREATE TABLE Rol ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    rol VARCHAR(50) NOT NULL UNIQUE 
); 

-- Privilegio (Privilegio) 
CREATE TABLE Privilegio ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    titulo VARCHAR(100) NOT NULL UNIQUE, 
    enlace VARCHAR(255) 
); 

-- Puede_hacer (Permisos) 
CREATE TABLE Puede_hacer ( 
    id_rol INT, 
    id_privilegio INT, 
    CONSTRAINT pk_puede_hacer PRIMARY KEY (id_rol, id_privilegio), 
    CONSTRAINT fk_puede_hacer_rol FOREIGN KEY (id_rol) REFERENCES Rol(id), 
    CONSTRAINT fk_puede_hacer_privilegio FOREIGN KEY (id_privilegio) REFERENCES Privilegio(id) 
); 

-- Centro_veterinario (Centro Veterinario) 
CREATE TABLE Centro_veterinario ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(100) NOT NULL, 
    telefono VARCHAR(20), 
    correo VARCHAR(100) 
); 

-- Grupo (Grupo) - Estructura de grupos de voluntarios/responsables 
CREATE TABLE Grupo ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(100) NOT NULL, 
    id_ayuntamiento INT NOT NULL, 
    CONSTRAINT fk_grupo_ayuntamiento FOREIGN KEY (id_ayuntamiento) REFERENCES Ayuntamiento(id) 
); 

-- Persona (Persona) 
CREATE TABLE Persona ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(50) NOT NULL, 
    apellidos VARCHAR(100) NOT NULL, 
    usuario VARCHAR(50) UNIQUE, 
    contraseña VARCHAR(255),  
    id_rol INT NOT NULL, 
    id_grupo INT, 
    id_ayuntamiento INT, 
    id_centro_veterinario INT, 
    CONSTRAINT fk_persona_rol FOREIGN KEY (id_rol) REFERENCES Rol(id), 
    CONSTRAINT fk_persona_grupo FOREIGN KEY (id_grupo) REFERENCES Grupo(id), 
    CONSTRAINT fk_persona_ayuntamiento FOREIGN KEY (id_ayuntamiento) REFERENCES Ayuntamiento(id), 
    CONSTRAINT fk_persona_centro_veterinario FOREIGN KEY (id_centro_veterinario) REFERENCES Centro_veterinario(id) 
); 

-- Colonia (Colonia) 
CREATE TABLE Colonia ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    latitud DECIMAL(10, 8) NOT NULL, 
    longitud DECIMAL(11, 8) NOT NULL, 
    descripción TEXT, 
    id_ayuntamiento INT NOT NULL, 
    CONSTRAINT fk_colonia_ayuntamiento FOREIGN KEY (id_ayuntamiento) REFERENCES Ayuntamiento(id) 
); 

-- Gato (Gato) 
CREATE TABLE Gato ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    num_chip VARCHAR(50) UNIQUE, -- Puede no tener chip, pero si lo tiene, es único 
    descripción TEXT, 
    foto VARCHAR(255), 
    id_colonia INT NOT NULL, 
    CONSTRAINT fk_gato_colonia FOREIGN KEY (id_colonia) REFERENCES Colonia(id) 
); 

-- Historial (Historial - para seguimiento de ubicación del gato) 
CREATE TABLE Albirament ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    fecha DATETIME NOT NULL, 
    id_gato INT NOT NULL, 
    id_colonia_albirada INT NOT NULL,
    id_colonia_anterior INT,
    CONSTRAINT fk_albirament_gato FOREIGN KEY (id_gato) REFERENCES Gato(id),
    CONSTRAINT fk_albirament_colonia_albirada FOREIGN KEY (id_colonia_albirada) REFERENCES Colonia(id),
    CONSTRAINT fk_albirament_colonia_anterior FOREIGN KEY (id_colonia_anterior) REFERENCES Colonia(id)
); 

-- Marca (Marca de comida) 
CREATE TABLE Marca ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(100) NOT NULL UNIQUE, 
    kg_por_gato DECIMAL(10,2),
    calidad ENUM('Baja', 'Media', 'Alta', 'Premium') NOT NULL, 
    descripción TEXT 
); 

-- Subministrament (Suministro de Comida) 
CREATE TABLE Subministrament ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    fecha DATETIME NOT NULL, 
    descripción TEXT, 
    cantidad DECIMAL(10,2),
    id_colonia INT NOT NULL, 
    id_voluntario INT NOT NULL, -- Asumiendo que el voluntario es el que subministra 
    id_marca INT NOT NULL, 
    CONSTRAINT fk_subministrament_colonia FOREIGN KEY (id_colonia) REFERENCES Colonia(id), 
    CONSTRAINT fk_subministrament_voluntario FOREIGN KEY (id_voluntario) REFERENCES Persona(id), 
    CONSTRAINT fk_subministrament_marca FOREIGN KEY (id_marca) REFERENCES Marca(id) 
); 

-- Visita (Visita de Responsable a la Colonia) 
CREATE TABLE Visita ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    fecha DATETIME NOT NULL, 
    comentario TEXT, 
    id_responsable INT NOT NULL, 
    id_colonia INT NOT NULL, 
    CONSTRAINT fk_visita_responsable FOREIGN KEY (id_responsable) REFERENCES Persona(id), 
    CONSTRAINT fk_visita_colonia FOREIGN KEY (id_colonia) REFERENCES Colonia(id) 
); 

-- Campaña (Campaña) 
CREATE TABLE Campaña ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    fecha_inicio DATE NOT NULL, 
    fecha_fin DATE NOT NULL, 
    tipo ENUM('Esterilización', 'Implementación chips', 'Vacunación') NOT NULL, 
    tipo_vacunación VARCHAR(100), -- NULL si el tipo no es 'Vacunación' 
    id_responsable INT NOT NULL, 
    id_centro_veterinario INT NOT NULL, 
    id_colonia INT NOT NULL, 
    CONSTRAINT fk_campaña_responsable FOREIGN KEY (id_responsable) REFERENCES Persona(id), 
    CONSTRAINT fk_campaña_centro_veterinario FOREIGN KEY (id_centro_veterinario) REFERENCES Centro_veterinario(id), 
    CONSTRAINT fk_campaña_colonia FOREIGN KEY (id_colonia) REFERENCES Colonia(id) 
); 

-- Acción (Acción veterinaria individual sobre un gato en una campaña) 
CREATE TABLE Acción ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    fecha DATETIME NOT NULL, 
    comentario TEXT, -- Comentario del veterinario al terminar 
    id_campaña INT NOT NULL, 
    id_gato INT NOT NULL, 
    CONSTRAINT fk_accion_campaña FOREIGN KEY (id_campaña) REFERENCES Campaña(id), 
    CONSTRAINT fk_accion_gato FOREIGN KEY (id_gato) REFERENCES Gato(id) 
); 

-- Participación (Participación de profesionales en una Acción) 
CREATE TABLE Participación ( 
    id_acción INT, 
    id_veterinario INT, 
    tipo ENUM('Manescal', 'Auxiliar', 'Otros') NOT NULL, 
    PRIMARY KEY (id_acción, id_veterinario), 
    CONSTRAINT fk_participacion_accion FOREIGN KEY (id_acción) REFERENCES Acción(id), 
    CONSTRAINT fk_participacion_veterinario FOREIGN KEY (id_veterinario) REFERENCES Persona(id) 
); 

-- Cementerio (Cementerio) 
CREATE TABLE Cementerio ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    nombre VARCHAR(100) NOT NULL, 
    capacidad INT NOT NULL, 
    calle VARCHAR(100), 
    número VARCHAR(10) 
); 

-- Solicitud_retirada (Solicitud de Retirada) 
CREATE TABLE Solicitud_retirada ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    fecha DATETIME NOT NULL, 
    estado ENUM('Pendiente', 'Aprobada', 'Rechazada', 'Completada') NOT NULL, 
    id_responsable INT NOT NULL, 
    id_cementerio INT, -- Puede ser NULL si la solicitud es rechazada o pendiente 
    id_gato INT NOT NULL, 
    CONSTRAINT fk_solicitud_retirada_responsable FOREIGN KEY (id_responsable) REFERENCES Persona(id), 
    CONSTRAINT fk_solicitud_retirada_cementerio FOREIGN KEY (id_cementerio) REFERENCES Cementerio(id), 
    CONSTRAINT fk_solicitud_retirada_gato FOREIGN KEY (id_gato) REFERENCES Gato(id) 
);