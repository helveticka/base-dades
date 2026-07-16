USE BD2SQLITO;

-- PAIS
INSERT INTO Pais (nombre) VALUES 
    ('España');

-- COMUNIDAD_AUTONOMA
INSERT INTO Comunidad_autonoma (nombre, id_pais) VALUES 
    ('Illes Balears', 1),
    ('Cataluña', 1),
    ('Comunidad de Madrid', 1);

-- PROVINCIA
INSERT INTO Provincia (nombre, id_comunidad_autonoma) VALUES 
    ('Illes Balears', 1),
    ('Barcelona', 2),
    ('Madrid', 3);

-- AYUNTAMIENTO
INSERT INTO Ayuntamiento (nombre, id_provincia) VALUES 
    ('Palma', 1),      -- ID 1
    ('Manacor', 1),    -- ID 2
    ('Calvià', 1),     -- ID 3
    ('Inca', 1),       -- ID 4
    ('Llucmajor', 1),  -- ID 5
    ('Santanyí', 1),   -- ID 6
    ('Eivissa', 1),    -- ID 7
    ('Badalona', 2);   -- ID 8

INSERT INTO Rol (rol) VALUES 
    ('Interesado'),      -- ID 1
    ('Voluntario'),       -- ID 2
    ('Responsable'),      -- ID 3
    ('Veterinario'),      -- ID 4
    ('Ayuntamiento'),     -- ID 5
    ('Centro');           -- ID 6

-- Privilegios
--  1  Crear Tareas              → Responsable
--  2  Ver tareas                → Voluntario, Responsable
--  3  Añadir Visita             → Responsable
--  4  Ver Incidencias           → Responsable
--  5  Avistamiento              → Responsable
--  6  Crear Campaña             → Responsable
--  7  Ver Campañas              → Responsable, Centro, Veterinario
--  8  Ver Acción                → Veterinario
--  9  Ver Gatos                 → Voluntario, Responsable
-- 10  Registrar Gato            → Responsable
-- 11  Ver Manadas               → Voluntario, Responsable
-- 12  Ver Grupos                → Ayuntamiento
-- 13  Crear Grupo               → Ayuntamiento
-- 14  Añadir Miembro            → Ayuntamiento
-- 15  Ver Interesados           → Ayuntamiento
-- 16  Registrar Manada          → Responsable
-- 17  Crear Solicitud           → Responsable
-- 18  Ver Solicitudes           → Centro, Responsable (solo las suyas)
-- 19  Añadir Vet                → Centro
-- 20  Añadir Centro             → Ayuntamiento
-- 21  Ver Veterinario           → Centro
-- PRIVILEGIO
INSERT INTO privilegio (titulo, enlace)
VALUES
    ("Crear Tareas", "/BD2SQLITO/core/crear_tareas.php"),
    ("Ver tareas", "/BD2SQLITO/core/ver_tareas.php"),
    ("Añadir Visita", "/BD2SQLITO/core/añadir_visita.php"),
    ("Ver Incidencias", "/BD2SQLITO/core/ver_incidencias.php"),
    ("Avistamiento", "/BD2SQLITO/core/albirament.php"),
    ("Crear Campaña", "/BD2SQLITO/campanas/crear_campana.php"),
    ("Ver Campañas", "/BD2SQLITO/campanas/ver_campanas_activas.php"),
    ("Ver Acción", "/BD2SQLITO/campanas/ver_accion.php"),
    ("Ver Gatos", "/BD2SQLITO/gatos/ver_gatos.php"),
    ("Registrar Gato", "/BD2SQLITO/gatos/registrar_gato.php"),
    ("Ver Manadas", "/BD2SQLITO/gatos/ver_manada.php"),
    ("Ver Grupos", "/BD2SQLITO/grupos/ver_grupos.php"),
    ("Crear Grupo", "/BD2SQLITO/grupos/crear_grupos.php"),
    ("Añadir Miembro", "/BD2SQLITO/grupos/añadir_miembros.php"),
    ("Ver Interesados", "/BD2SQLITO/grupos/ver_interesados.php"),
    ("Registrar Manada", "/BD2SQLITO/gatos/registrar_manada.php"),
    ("Crear solicitud", "/BD2SQLITO/gatos/solicitud_retirada.php"),
    ("Ver Solicitudes", "/BD2SQLITO/gatos/ver_solicitudes.php"),
    ("Añadir Vet", "/BD2SQLITO/grupos/anadir_vet.php"),
    ("Añadir Centro", "/BD2SQLITO/campanas/anadir_centro.php"),
    ("Ver Veterinario", "/BD2SQLITO/grupos/ver_veterinarios.php");

-- Asignación de privilegios a Voluntarios
INSERT INTO puede_hacer (id_rol, id_privilegio)
VALUES
    (2, 2),
    (2, 9),
    (2, 11);

-- Asignación de privilegios a Responsables
INSERT INTO puede_hacer (id_rol, id_privilegio)
VALUES
    (3, 1),
    (3, 2),
    (3, 3),
    (3, 4),
    (3, 5),
    (3, 6),
    (3, 7),
    (3, 9),
    (3, 10),
    (3, 11),
    (3, 16),
    (3, 17),
    (3, 18);

-- Asignación de privilegios a Veterinarios
INSERT INTO puede_hacer (id_rol, id_privilegio)
VALUES
    (4, 7);

-- Asignación de privilegios a Ayuntamientos
INSERT INTO puede_hacer (id_rol, id_privilegio)
VALUES
    (5, 12),
    (5, 13),
    (5, 14),
    (5, 15),
    (5, 20);

-- Asignación de privilegios a Centros
INSERT INTO puede_hacer (id_rol, id_privilegio)
VALUES
    (6, 7),
    (6, 18),
    (6, 19),
    (6, 21);

-- CENTRO_VETERINARIO
INSERT INTO Centro_veterinario (nombre, telefono, correo) VALUES 
    ('Clínica Felina Palma Centre', '971112233', 'palma-vet@felina.com'),   -- ID 1
    ('Veterinaria Manacor Cerdà', '971445566', 'vetmanacor@mail.com'),     -- ID 2
    ('Vet Amics Calvià', '971778899', 'amicsvet@mail.com'),             -- ID 3
    ('Hospital Veterinari Illes', '971900000', 'hvetilles@mail.com');    -- ID 4

-- GRUPO
INSERT INTO Grupo (nombre, id_ayuntamiento) VALUES 
    ('Grup Voluntaris de Palma', 1), -- ID 1
    ('Associació Felina Llevant', 2), -- ID 2
    ('Amics dels Gats Calvià', 3),   -- ID 3
    ('Voluntariat Inca Verd', 4);    -- ID 4

-- PERSONA
INSERT INTO Persona (nombre, apellidos, usuario, contraseña, id_rol, id_grupo, id_ayuntamiento, id_centro_veterinario) VALUES 
    -- Responsables y Voluntarios
    ('Marc', 'Morlà', 'marc_morla', 'pass123', 3, 1, 1, NULL),
    ('Antònia', 'Ripoll Bauzà', 'antonia_ripoll', 'pass123', 2, 1, 1, NULL),
    ('Miquel Àngel', 'Vila Sastre', 'miquel_vila', 'pass123', 3, 2, 2, NULL),
    ('Núria', 'Serra Femenies', 'nuria_serra', 'pass123', 2, 2, 2, NULL),
    ('Pere', 'Joan Ramos', 'pere_joan', 'pass123', 3, 3, 3, NULL),
    ('Elena', 'García Bosch', 'elena_garcia', 'pass123', 2, 3, 3, NULL),
    -- Veterinarios
    ('Catalina', 'Pou Amer', 'catalina_vet', 'pass123', 4, NULL, NULL, 1),
    ('Xavi', 'Martorell', 'xavi_vet', 'pass123', 4, NULL, NULL, 2),
    -- Ayuntamientos
    ('Joan', 'Ayuntamiento Palma', 'ayu_palma', 'pass123', 5, NULL, 1, NULL),
    ('Manel', 'Gestor Calvià', 'ayu_calvia', 'pass123', 5, NULL, 3, NULL),
    -- Centros
    ('Laura', 'Admin Clínica Palma', 'centro_palma', 'pass123', 6, NULL, NULL, 1),
    ('Andreu', 'Admin Vet Manacor', 'centro_manacor', 'pass123', 6, NULL, NULL, 2),
    -- Interesados
    ('Carlos', 'Ruiz Soler', 'carlos_ruiz', 'pass123', 1, NULL, 1, NULL),
    ('Marta', 'García López', 'marta_garcia', 'pass123', 1, NULL, 2, NULL),
    ('Jordi', 'Vidal Pons', 'jordi_vidal', 'pass123', 1, NULL, 3, NULL),
    ('Lluís', 'Pascual Roca', 'lluis_pascual', 'pass123', 1, NULL, 4, NULL),
    ('Maria', 'Torres Mayol', 'maria_torres', 'pass123', 1, NULL, 5, NULL),
    ('Toni', 'Vicens Bauzà', 'toni_vicens', 'pass123', 1, NULL, 6, NULL),
    ('Aina', 'Rosselló Mir', 'aina_rossello', 'pass123', 1, NULL, 7, NULL),
    ('Sergi', 'Mayol Seguí', 'sergi_mayol', 'pass123', 1, NULL, 8, NULL),
    ('Clara', 'Bennàsar Coll', 'clara_bennasar', 'pass123', 1, NULL, 1, NULL),
    ('Pau', 'Oliver Ripoll', 'pau_oliver', 'pass123', 1, NULL, 2, NULL);

-- COLONIA (10 Colonias más específicas)
INSERT INTO Colonia (latitud, longitud, descripción, id_ayuntamiento) VALUES 
    (39.576082, 2.650849, 'Plaça de Cort (Ajuntament)', 1),           -- ID 1
    (39.569485, 2.628873, 'Passeig des Born', 1),                    -- ID 2
    (39.578000, 2.642000, 'Parc de Ses Estacions', 1),               -- ID 3
    (39.697666, 3.195679, 'Plaça de sa Bassa', 2),                   -- ID 4
    (39.516584, 2.458921, 'Polígon de Son Bugadelles', 3),           -- ID 5
    (39.510000, 2.470000, 'Passeig de Palmanova', 3),                -- ID 6
    (39.721453, 2.999672, 'Mercat d''Inca', 4),                      -- ID 7
    (39.467000, 2.876000, 'Costa de Llucmajor', 5),                  -- ID 8
    (39.380000, 3.120000, 'Port de Santanyí', 6),                    -- ID 9
    (38.908000, 1.432000, 'Dalt Vila (Eivissa)', 7);                 -- ID 10

-- GATO (10 Gatos con coherencia de ubicación)
-- Simba empezó en la 1, ahora está en la 2 (según Albirament)
INSERT INTO Gato (num_chip, descripción, foto, id_colonia) VALUES 
    ('1001-SIMBA', 'Mascle taronja, molt docil. Habitual de Cort.', '/BD2SQLITO/images/gato.jpg', 2),
    ('1002-LUNA', 'Femella negra amb una taca blanca al pit.', '/BD2SQLITO/images/gato.jpg', 1),
    ('1003-MIXU', 'Mascle blanc i gris, esquerp.', '/BD2SQLITO/images/gato.jpg', 3),
    (NULL, 'Femella tricolor, vella, sense xip.', '/BD2SQLITO/images/gato.jpg', 4),
    ('1004-COCO', 'Mascle siamès, molt territorial.', '/BD2SQLITO/images/gato.jpg', 1),
    ('1005-BELLA', 'Femella marró tigrada, jove.', '/BD2SQLITO/images/gato.jpg', 5),
    ('1006-TIGRE', 'Mascle gran, gris tigrat.', '/BD2SQLITO/images/gato.jpg', 7),
    (NULL, 'Femella blanca petita.', '/BD2SQLITO/images/gato.jpg', 6),
    ('1007-ROCKY', 'Mascle negre, fort.', '/BD2SQLITO/images/gato.jpg', 2),
    ('1008-NINA', 'Femella crema, molt carinyosa.', '/BD2SQLITO/images/gato.jpg', 4);

-- ALBIRAMENT (Movimientos registrados)
INSERT INTO Albirament (fecha, id_gato, id_colonia_albirada, id_colonia_anterior) VALUES 
    ('2025-12-05 18:00:00', 1, 2, 1),    -- Simba se ha desplazado al Born (su id_colonia en Gato es 2)
    ('2025-12-15 11:00:00', 9, 2, 1);    -- Rocky también se ha movido al Born

-- MARCA
INSERT INTO Marca (nombre, kg_por_gato, calidad, descripción) VALUES 
    ('Royal Canin Feline', 0.45, 'Premium', 'Alta digestibilitat i control de boles de pèl.'),
    ('Purina Pro Plan', 0.40, 'Alta', 'Reforç del sistema immunitari.'),
    ('Affinity Advance', 0.40, 'Alta', 'Equilibri nutricional òptim.'),
    ('Brekkies Excel', 0.35, 'Media', 'Opció econòmica però completa.');

-- SUBMINISTRAMENT
INSERT INTO Subministrament (fecha, descripción, cantidad, id_colonia, id_voluntario, id_marca) VALUES 
    ('2025-12-18 08:00:00', 'Ronda matinal del Born', 0.9, 2, 2, 1),
    ('2025-12-18 09:00:00', 'Subministrament a Cort', 0.8, 1, 2, 2),
    ('2025-12-19 07:45:00', 'Ronda Manacor (sa Bassa)', 0.8, 4, 4, 3);

-- VISITA
INSERT INTO Visita (fecha, comentario, id_responsable, id_colonia) VALUES 
    ('2025-12-10 11:00:00', 'Colònia de Cort en bon estat. He vist en Simba més prim.', 1, 1),
    ('2025-12-16 17:00:00', 'Visita al Born. Confirmada presència d''en Simba i Rocky. Sembla que s''han adaptat.', 1, 2);

-- CAMPAÑA
INSERT INTO Campaña (fecha_inicio, fecha_fin, tipo, tipo_vacunación, id_responsable, id_centro_veterinario, id_colonia) VALUES 
    ('2025-12-20', '2025-12-22', 'Esterilización', NULL, 1, 1, 2),
    ('2026-01-05', '2026-01-06', 'Vacunación', 'Trivalent', 3, 2, 4);

-- ACCIÓN
INSERT INTO Acción (fecha, comentario, id_campaña, id_gato) VALUES 
    ('2025-12-20 10:00:00', 'Esterilització d''en Rocky finalitzada amb èxit.', 1, 9),
    ('2025-12-21 11:30:00', 'Revisió de na Luna abans d''esterilitzar-la.', 1, 2);

-- PARTICIPACIÓN
INSERT INTO Participación (id_acción, id_veterinario, tipo) VALUES 
    (1, 7, 'Manescal'),  -- Catalina opera a Rocky
    (2, 7, 'Manescal');

-- CEMENTERIO
INSERT INTO Cementerio (nombre, capacidad, calle, número) VALUES 
    ('Cementiri de Son Reus', 500, 'Camí de Son Reus', 's/n'),
    ('Incineradora de Palma', 1000, 'Son Castelló', '15');

-- SOLICITUD_RETIRADA
INSERT INTO Solicitud_retirada (fecha, estado, id_responsable, id_cementerio, id_gato) VALUES 
    ('2025-12-15 09:00:00', 'Pendiente', 1, NULL, 3); -- Solicitud para Mixu 
