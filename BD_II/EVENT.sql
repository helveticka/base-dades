USE BD2SQLITO;

SET GLOBAL event_scheduler=ON;

CREATE EVENT backup_diario_colonies
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 HOUR  -- empieza mañana a la 1am
DO
    CALL backup_colonies();