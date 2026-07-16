<?php
// ver_accion.php - Ver detalles de una acción
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Obtener información de la acción
$consulta_accion = "SELECT a.id, a.fecha, a.comentario, g.id AS gato_id, g.num_chip, g.descripción AS gato_desc, 
                           c.id AS campana_id, c.tipo AS campana_tipo, c.fecha_inicio AS campana_inicio, c.fecha_fin AS campana_fin, 
                           p.nombre AS responsable_nombre, p.apellidos AS responsable_apellidos
                    FROM Acción a
                    JOIN Gato g ON g.id = a.id_gato
                    JOIN Campaña c ON c.id = a.id_campaña
                    JOIN Persona p ON p.id = c.id_responsable
                    WHERE a.id = $id";
$resultado_accion = mysqli_query($con, $consulta_accion);
$accion = mysqli_fetch_assoc($resultado_accion);

if (!$accion) {
    die("<h2>La acción no existe o el ID es inválido.</h2>");
}

// Profesionales participantes
$consulta_part = "SELECT per.nombre, per.apellidos, pa.tipo
                  FROM Participación pa
                  JOIN Persona per ON per.id = pa.id_veterinario
                  WHERE pa.id_acción = $id";
$resultado_part = mysqli_query($con, $consulta_part);

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ver Acción</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Detalles de la Acción ID: '.$accion['id'].'</h1>
        
        <div class="section">
            <p><strong>Fecha:</strong> '.$accion['fecha'].'</p>
            <p><strong>Comentario:</strong> '.$accion['comentario'].'</p>
        </div>

        <div class="section">
            <h3>Gato intervenido</h3>
            <p><strong>ID Gato:</strong> '.$accion['gato_id'].'</p>
            <p><strong>Descripción:</strong> '.$accion['gato_desc'].'</p>
            <p><strong>Chip:</strong> '.($accion['num_chip'] ?: "Sin chip").'</p>
        </div>

        <div class="section">
            <h3>Campaña relacionada</h3>
            <p><strong>ID Campaña:</strong> '.$accion['campana_id'].'</p>
            <p><strong>Tipo:</strong> '.$accion['campana_tipo'].'</p>
            <p><strong>Fecha Inicio:</strong> '.$accion['campana_inicio'].'</p>
            <p><strong>Fecha Fin:</strong> '.$accion['campana_fin'].'</p>
            <p><a href="/BD2SQLITO/campanas/ver_campana.php?id='.$accion['campana_id'].'&Usuario='.$usuario.'&Contraseña='.$contraseña.'">➜ Ver Campaña</a></p>
        </div>

        <div class="section">
            <h3>Responsable de Campaña</h3>
            <p>'.$accion['responsable_nombre'].' '.$accion['responsable_apellidos'].'</p>
        </div>

        <div class="section">
            <h3>Profesionales participantes</h3>';

if (mysqli_num_rows($resultado_part) == 0) {
    $html .= '<p>No hay profesionales registrados en esta acción.</p>';
} else {
    $html .= '<ul>';
    while ($p = mysqli_fetch_assoc($resultado_part)) {
        $html .= '<li>'.$p['nombre'].' '.$p['apellidos'].' ('.$p['tipo'].')</li>';
    }
    $html .= '</ul>';
}

$html .= '</div>
        <br>
        <form action="/BD2SQLITO/campanas/ver_campana.php" method="get">
            <input type="hidden" name="id" value="'.$accion['campana_id'].'">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Volver a la Campaña">
        </form>
    </div>
</body>
</html>';

echo $html;

mysqli_close($con);
?>