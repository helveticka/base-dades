-- CONSULTA A
-- Listar los 3 clientes de tipo empresa que han gastado más dinero en compras durante el último año, mostrando su nombre comercial, el número total de unidades de productos compradas y el importe gastado en compras. Ordenar el resultado de mayor a menor gasto total.
SELECT 
    E.nomEmpresa AS nom_comercial,
    SUM(QV.unitats) AS total_unitats,
    SUM(QV.unitats * P.preuUnitari) AS total_despesa
FROM EMPRESA E
JOIN VENDA V 
    ON E.idFiscal = V.idClient AND V.data >= CURDATE() - INTERVAL 1 YEAR
JOIN QUANTITAT_VENUDA QV 
    ON V.idVenda = QV.idVenda
JOIN QUANTITAT_CÀRREGA QC 
    ON QV.idQCarrega = QC.idQCarrega
JOIN LOT L 
    ON QC.numLot = L.numeroLot
JOIN PRODUCTE P 
    ON L.idProducte = P.referencia
GROUP BY E.idFiscal
ORDER BY total_despesa DESC
LIMIT 3;

-- CONSULTA B
-- Calcular el número total de ventas realizadas en los últimos 6 meses, agrupadas por almacén y vendedor. Mostrar la matrícula del vehículo usado para realizar la venta, el código y el nombre del almacén, el nombre y los apellidos del vendedor. Ordenar el resultado por el número de ventas realizadas.
SELECT
    ven.idVehicle AS MatriculaVehicle,
    mag.idMagatzem AS idMagatzem,
    mag.nom AS NomMagatzem,
    ven.nomVenedor AS NomVenedor,
    COUNT(venv.idVenda) AS TotalVendes
FROM VENEDOR ven
INNER JOIN VENDA venv
    ON ven.idVenedor = venv.idVenedor
    AND venv.data >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
INNER JOIN VEHICLE veh
    ON ven.idVehicle = veh.matricula
INNER JOIN MAGATZEM mag
    ON ven.idMagatzem = mag.idMagatzem
GROUP BY ven.idVehicle, mag.idMagatzem, mag.nom, ven.nomVenedor
ORDER BY TotalVendes DESC;

-- CONSULTA C
-- Calcular el número total de ventas realizadas en los últimos 6 meses, agrupadas por almacén y vendedor. Mostrar la matrícula del vehículo usado para realizar la venta, el código y el nombre del almacén, el nombre y los apellidos del vendedor. Ordenar el resultado por el número de ventas realizadas.
SELECT
    ven.idVehicle AS MatriculaVehicle,
    mag.idMagatzem AS idMagatzem,
    mag.nom AS NomMagatzem,
    ven.nomVenedor AS NomVenedor,
    COUNT(venv.idVenda) AS TotalVendes
FROM VENEDOR ven
INNER JOIN VENDA venv
    ON ven.idVenedor = venv.idVenedor
    AND venv.data >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
INNER JOIN VEHICLE veh
    ON ven.idVehicle = veh.matricula
INNER JOIN MAGATZEM mag
    ON ven.idMagatzem = mag.idMagatzem
GROUP BY ven.idVehicle, mag.idMagatzem, mag.nom, ven.nomVenedor
ORDER BY TotalVendes DESC;

-- CONSULTA D
-- Extraer un listado de todos los productos, mostrando el nombre, la categoría y el número total de unidades vendidas durante el último año. Ordenar el resultado alfabéticamente por el nombre de la categoría y por el número de unidades vendidas en orden descendente.
SELECT 
  P.nomComercial AS NomProducte, 
  C.categoria AS Categoria, 
  SUM(QV.unitats) AS UnitatsVenudes 
FROM 
  PRODUCTE P 
  JOIN CATEGORIA C 
ON P.idCategoria = C.idCategoria 
  JOIN LOT L 
ON P.referencia = L.idProducte 
  JOIN QUANTITAT_CÀRREGA QC 
ON QC.numLot = L.numeroLot 
  JOIN QUANTITAT_VENUDA QV 
ON QV.idQCarrega = QC.idQCarrega 
  JOIN VENDA V 
ON V.idVenda = QV.idVenda 
WHERE 
   V.data >= CURRENT_DATE - INTERVAL 365 DAY
GROUP BY 
  P.referencia, C.categoria
ORDER BY 
  Categoria ASC, 
  UnitatsVenudes DESC;

-- CONSULTA E
-- Obtener un listado de los productos que tienen menos de 100 unidades en stock, considerando el stock en los distintos almacenes. Mostrar el nombre del producto, su descripción y la cantidad total en stock. Ordenar los resultados de menor a mayor stock disponible.
SELECT
    p.referencia AS id_producto,
    p.nomComercial AS nombre_producto,
    p.descripcio AS descripcion,
    SUM(l.quantitat) - SUM(qv.unitats) AS stock_disponible
FROM PRODUCTE p
LEFT JOIN LOT l ON p.referencia = l.idProducte AND l.caducitat > CURDATE()
LEFT JOIN QUANTITAT_CÀRREGA qc ON l.numeroLot = qc.numLot
LEFT JOIN QUANTITAT_VENUDA qv ON qc.idQCarrega = qv.idQCarrega
GROUP BY p.referencia, p.nomComercial, p.descripcio
HAVING stock_disponible < 100 AND stock_disponible >= 0
ORDER BY stock_disponible ASC;

-- CONSULTA F
-- Obtener un listado de los lotes de productos que caducan en los próximos 30 días. Para cada lote, se debe mostrar el nombre y la descripción del producto asociado, el número de lote, la fecha de caducidad, el almacén donde se encuentra el lote y la cantidad de días restantes hasta la caducidad. Ordenar el listado por fecha de caducidad de forma que los lotes más próximos a caducar se muestren primero.
SELECT 
    P.nomComercial AS nom_producte,
    P.descripcio,
    L.numeroLot,
    L.caducitat,
    M.nom AS nom_magatzem,
    DATEDIFF(L.caducitat, CURDATE()) AS dies_fins_caducitat
FROM LOT L
JOIN PRODUCTE P 
ON L.idProducte = P.referencia
JOIN MAGATZEM M 
ON L.idMagatzem = M.idMagatzem
WHERE 
L.caducitat BETWEEN CURDATE() 
AND CURDATE() + INTERVAL 30 DAY
ORDER BY L.caducitat ASC;

-- CONSULTA G
-- Mostrar los datos de contacto de los clientes particulares que aún no han realizado ninguna compra.
SELECT 
    P.nomParticular,
    P.telefonParticular,
    P.emailParticular
FROM 
    PARTICULAR P
LEFT JOIN VENDA V 
ON P.idFiscal = V.idClient
WHERE 
    V.idVenda IS NULL;

-- CONSULTA H
-- Mostrar los almacenes con el responsable actual y el responsable anterior. Para cada almacén, se mostrará el nombre del almacén, el nombre y apellidos del responsable actual (si lo tiene) y del responsable anterior (si lo hay). Ordenar el resultado por el nombre del almacén y por orden de antigüedad de los responsables.
SELECT
    M.nom AS NomMagatzem,
    RA.nomResponsable AS ResponsableActual,
    RP.nomResponsable AS ResponsableAnterior
FROM
    MAGATZEM M
LEFT JOIN PERSONA_RESPONSABLE RA
    ON M.idMagatzem = RA.idMagatzem
    AND RA.dataFi IS NULL
LEFT JOIN PERSONA_RESPONSABLE RP
    ON M.idMagatzem = RP.idMagatzem
    AND RP.dataFi = (
        SELECT MAX(dataFi)
        FROM PERSONA_RESPONSABLE
        WHERE idMagatzem = M.idMagatzem
          AND dataFi IS NOT NULL
    )
ORDER BY
    M.nom ASC,
    RP.dataFi ASC;
