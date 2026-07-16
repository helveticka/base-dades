<?php
// ver_campana.php - Ver detalles de una campaña
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Obtener detalles de la campaña
$consulta_campana = "SELECT c.id, c.fecha_inicio, c.fecha_fin, c.tipo, c.tipo_vacunación, 
                            p.nombre AS responsable_nombre, p.apellidos AS responsable_apellidos, 
                            cv.nombre AS centro_nombre, col.descripción AS colonia_desc
                     FROM Campaña c
                     JOIN Persona p ON p.id = c.id_responsable
                     JOIN Centro_veterinario cv ON cv.id = c.id_centro_veterinario
                     JOIN Colonia col ON col.id = c.id_colonia
                     WHERE c.id = $id";
$resultado_campana = mysqli_query($con, $consulta_campana);
$campana = mysqli_fetch_assoc($resultado_campana);

if (!$campana) {
    die("<h2>La campaña no existe.</h2>");
}

// Acciones sobre gatos
$consulta_acc = "SELECT a.id, a.fecha, a.comentario, g.id AS gato_id, g.num_chip, g.descripción AS gato_desc
                 FROM Acción a
                 JOIN Gato g ON g.id = a.id_gato
                 WHERE a.id_campaña = $id";
$resultado_acc = mysqli_query($con, $consulta_acc);

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detalles de Campaña</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Campaña ID: '.$campana['id'].'</h1>

        <div class="section">
            <p><strong>Tipo:</strong> '.$campana['tipo'].'</p>';
if ($campana['tipo'] == "Vacunación") {
    $html .= '<p><strong>Tipo de Vacunación:</strong> '.$campana['tipo_vacunación'].'</p>';
}
$html .= '  <p><strong>Fecha Inicio:</strong> '.$campana['fecha_inicio'].'</p>
            <p><strong>Fecha Fin:</strong> '.$campana['fecha_fin'].'</p>
        </div>

        <div class="section">
            <p><strong>Responsable:</strong> '.$campana['responsable_nombre'].' '.$campana['responsable_apellidos'].'</p>
            <p><strong>Centro Veterinario:</strong> '.$campana['centro_nombre'].'</p>
            <p><strong>Colonia:</strong> '.$campana['colonia_desc'].'</p>
        </div>

        <h3>Acciones sobre Gatos</h3>';

if (mysqli_num_rows($resultado_acc) == 0) {
    $html .= '<p>No hay acciones registradas para esta campaña.</p>';
} else {
    while ($acc = mysqli_fetch_assoc($resultado_acc)) {
        $html .= '<div style="border-left: 4px solid #ccc; padding-left: 10px; margin-bottom: 15px;">
                    <p><strong>Acción ID:</strong> '.$acc['id'].' - <strong>Fecha:</strong> '.$acc['fecha'].'</p>
                    <p><strong>Gato:</strong> '.$acc['gato_desc'].' ('.($acc['num_chip'] ?: "Sin chip").')</p>
                    <p><strong>Comentario:</strong> '.$acc['comentario'].'</p>
                    <p><a href="/BD2SQLITO/campanas/ver_accion.php?id='.$acc['id'].'&Usuario='.$usuario.'&Contraseña='.$contraseña.'">➜ Ver detalles de la acción</a></p>
                  </div>';
    }
}

$html .= '  <br>
            <form action="/BD2SQLITO/campanas/anadir_accion.php" method="get">
                <input type="hidden" name="id_campana" value="'.$id.'">
                <input type="hidden" name="Usuario" value="'.$usuario.'">
                <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                <input type="submit" value="Añadir Acción">
            </form>
            <br>
            <form action="/BD2SQLITO/campanas/ver_campanas_activas.php" method="get">
                <input type="hidden" name="Usuario" value="'.$usuario.'">
                <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                <input type="submit" value="Volver a Campañas Activas">
            </form>
    </div>
</body>
</html>';

echo $html;

mysqli_close($con);
?>