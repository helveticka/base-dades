<?php
// ver_campanas_activas.php - Ver campañas que están activas actualmente
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Consultar campañas activas (Hoy entre inicio y fin)
$consulta_campanas = "SELECT c.id, c.fecha_inicio, c.fecha_fin, c.tipo, c.tipo_vacunación, 
                             p.nombre AS responsable_nombre, p.apellidos AS responsable_apellidos, 
                             cv.nombre AS centro_nombre, col.descripción AS colonia_desc
                      FROM Campaña c
                      JOIN Persona p ON p.id = c.id_responsable
                      JOIN Centro_veterinario cv ON cv.id = c.id_centro_veterinario
                      JOIN Colonia col ON col.id = c.id_colonia
                      WHERE c.fecha_inicio <= CURDATE() AND c.fecha_fin >= CURDATE()
                      ORDER BY c.fecha_inicio ASC";
$resultado_campanas = mysqli_query($con, $consulta_campanas);

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Campañas Activas</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Campañas Activas Actualmente</h1>';

if (mysqli_num_rows($resultado_campanas) == 0) {
    $html .= '<p>No hay campañas activas actualmente.</p>';
} else {
    while ($c = mysqli_fetch_assoc($resultado_campanas)) {
        $html .= '<div style="border-left: 5px solid #4CAF50; padding-left: 15px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    <h3>'.$c['tipo'].' (ID: '.$c['id'].')</h3>
                    <p><strong>Fecha:</strong> '.$c['fecha_inicio'].' al '.$c['fecha_fin'].'</p>';
        if ($c['tipo'] == "Vacunación") {
            $html .= '<p><strong>Vacunación:</strong> '.$c['tipo_vacunación'].'</p>';
        }
        $html .= '  <p><strong>Responsable:</strong> '.$c['responsable_nombre'].' '.$c['responsable_apellidos'].'</p>
                    <p><strong>Centro:</strong> '.$c['centro_nombre'].'</p>
                    <p><strong>Colonia:</strong> '.$c['colonia_desc'].'</p>
                    <p><a href="/BD2SQLITO/campanas/ver_campana.php?id='.$c['id'].'&Usuario='.$usuario.'&Contraseña='.$contraseña.'">➜ Ver detalles de la campaña</a></p>
                  </div>';
    }
}

$html .= ' 
             <br>
            <form action="/BD2SQLITO/core/principal.php" method="get">
                <input type="hidden" name="Usuario" value="'.$usuario.'">
                <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                <input type="submit" value="Volver al Menú Principal">
            </form>
    </div>
</body>
</html>';

echo $html;

mysqli_close($con);
?>