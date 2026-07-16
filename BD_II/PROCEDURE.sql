USE BD2SQLITO;

DELIMITER //

CREATE PROCEDURE backup_colonies_simple()
BEGIN
    DECLARE backup_ok BOOL DEFAULT TRUE;
    DECLARE backup_msg TEXT DEFAULT '';
    
    -- Limpiar tablas backup antes de llenarlas
    DELETE FROM Gato_backup;
    DELETE FROM Colonia_backup;
    DELETE FROM Ayuntamiento_backup;

    -- Manejo de errores básico
    BEGIN
        DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
        BEGIN
            SET backup_ok = FALSE;
            SET backup_msg = CONCAT(backup_msg, 'Error durante el backup. ');
        END;

        -- Ayuntamiento
        INSERT INTO Ayuntamiento_backup (id, nombre, id_provincia)
        SELECT id, nombre, id_provincia
        FROM Ayuntamiento;

        -- Colonia
        INSERT INTO Colonia_backup (id, latitud, longitud, descripción, id_ayuntamiento)
        SELECT id, latitud, longitud, descripción, id_ayuntamiento
        FROM Colonia;

        -- Gato
        INSERT INTO Gato_backup (id, num_chip, descripción, foto, id_colonia)
        SELECT id, num_chip, descripción, foto, id_colonia
        FROM Gato;
    END;

    -- Registrar el resultado en Backup_log
    INSERT INTO Backup_log (fecha, exito, mensaje)
    VALUES (NOW(), backup_ok, backup_msg);
END;
//
DELIMITER ;