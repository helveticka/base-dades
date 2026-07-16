USE BD2SQLITO;

DELIMITER //

CREATE TRIGGER tr_actualiza_colonia_gato
AFTER INSERT ON Albirament
FOR EACH ROW
BEGIN
    -- Cuando se inserta un avistamiento, actualiza el id_colonia del gato
    UPDATE Gato
    SET id_colonia = NEW.id_colonia_albirada
    WHERE id = NEW.id_gato;
END//

DELIMITER ;