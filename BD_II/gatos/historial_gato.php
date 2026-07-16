<?php
// historial_gato.php - historial (avistamientos) de ubicaciones de un gato
// parámetros recibidos
$id_gato = isset($_GET['id']) ? intval($_GET['id']) : 0;
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

if ($id_gato <= 0) {
    die("ID de gato no válido.");
}

// obtener datos del gato pasado por parámetro
$resultado_gato = mysqli_query($con, "SELECT * FROM Gato WHERE id = $id_gato");
$gato = mysqli_fetch_assoc($resultado_gato);

if (!$gato) {
    die("Gato no encontrado.");
}

// obtener avistamientos del gato
$consulta_hist = "SELECT h.fecha, 
                        c_anterior.descripción AS colonia_anterior, 
                        c_albirada.descripción AS colonia_albirada
                   FROM Albirament h
                   JOIN Colonia c_albirada ON h.id_colonia_albirada = c_albirada.id
                   JOIN Colonia c_anterior ON h.id_colonia_anterior = c_anterior.id
                   WHERE h.id_gato = $id_gato
                   ORDER BY h.fecha DESC";
$resultado_hist = mysqli_query($con, $consulta_hist);

//html de la página
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Albirament del Gato</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Albirament del Gato #'.$gato['id'].'</h1>

        <h3>Datos del Gato</h3>
        <ul>
            <li><strong>Nº Chip:</strong> '.($gato['num_chip'] ?: '—').'</li>
            <li><strong>Descripción:</strong> '.nl2br($gato['descripción']).'</li>
            <li><strong>Foto:</strong>';
if (!empty($gato['foto'])) {
    $html .= '<br><img src="'.$gato['foto'].'" alt="Foto gato" width="150" style="border-radius: 8px; margin-top: 10px;">';
} else {
    $html .= ' <span style="color:#999;">Sin foto</span>';
}
$html .= '  </li>
        </ul>

        <h3>Albirament de Ubicaciones</h3>';

if (mysqli_num_rows($resultado_hist) == 0) {
    $html .= '<p>Este gato todavía no tiene Albirament registrado.</p>';
} else {
    $html .= '<table>
                <tr>
                    <th>Fecha</th>
                    <th>Colonia Anterior</th>
                    <th>Colonia Nueva</th>
                </tr>';
    while ($h = mysqli_fetch_assoc($resultado_hist)) {
        $html .= '<tr>
                    <td>'.$h['fecha'].'</td>
                    <td>'.$h['colonia_anterior'].'</td>
                    <td>'.$h['colonia_albirada'].'</td>
                  </tr>';
    }
    $html .= '</table>';
}

$html .= '  <br>
            <form action="/BD2SQLITO/gatos/ver_gatos.php" method="get">
                <input type="hidden" name="Usuario" value="'.$usuario.'">
                <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                <input type="submit" value="Volver a Gatos">
            </form>
            <br>
            <form action="/BD2SQLITO/gatos/ver_manada.php" method="get">
                <input type="hidden" name="Usuario" value="'.$usuario.'">
                <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                <input type="submit" value="Volver a Manadas">
            </form>
    </div>
</body>
</html>';

echo $html;

mysqli_close($con);
?>