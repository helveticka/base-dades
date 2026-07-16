<?php
// solicitud_retirada.php - crear nueva solicitud de retirada
$usuario_log = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña_log = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

//consulta para seleccionar el ayuntamiento de la persona loggeada
$q_ayunt = "SELECT id_ayuntamiento
            FROM Persona
            WHERE usuario = '$usuario_log' AND contraseña = '$contraseña_log'
            LIMIT 1";

$r_user = mysqli_query($con, $q_ayunt);
if (!$r_user || mysqli_num_rows($r_user) == 0) {
    die("Error: usuario/contraseña inválidos o no encontrados.");
}
$u = mysqli_fetch_assoc($r_user);
$id_ayuntamiento   = (int)$u['id_ayuntamiento'];

//si nos llega la creación de la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_responsable = intval($_POST['id_responsable']);
    $id_gato = intval($_POST['id_gato']);
    $estado = 'Pendiente';
    $fecha = date('Y-m-d H:i:s');
    $id_cementerio = !empty($_POST['id_cementerio']) ? intval($_POST['id_cementerio']) : 'NULL';

    $sql_insert = "INSERT INTO Solicitud_retirada (fecha, estado, id_responsable, id_cementerio, id_gato)
                   VALUES ('$fecha', '$estado', $id_responsable, $id_cementerio, $id_gato)";
    
    if (mysqli_query($con, $sql_insert)) {
        $mensaje = "Solicitud de retirada creada correctamente.";
    } else {
        $mensaje = "Error al crear la solicitud: " . mysqli_error($con);
    }
}

// obtener responsables para el desplegable
$sql_resp = "SELECT id, nombre, apellidos FROM Persona WHERE id_rol = 3 AND id_ayuntamiento = $id_ayuntamiento ORDER BY nombre, apellidos";
$resultado_resp = mysqli_query($con, $sql_resp);

// obtener cementerios para el desplegable
$sql_cem = "SELECT id, nombre FROM Cementerio ORDER BY nombre";
$resultado_cem = mysqli_query($con, $sql_cem);

// obtener gatos para el desplegable
$sql_gatos = "SELECT id, num_chip, descripción FROM Gato ORDER BY id";
$resultado_gatos = mysqli_query($con, $sql_gatos);

//html de la página
echo '<html>
<head>
    <meta charset="utf-8">
    <title>Nueva Solicitud de Retirada</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Crear Nueva Solicitud de Retirada</h1>';

if (isset($mensaje)) {
    $color = (strpos($mensaje, 'Error') === false) ? 'green' : 'red';
    echo '<p style="color:'.$color.';">'.$mensaje.'</p>';
}

echo '  <form method="post">
            <label for="id_responsable">Responsable:</label>
            <select id="id_responsable" name="id_responsable" required>
                <option value="">-- Elige responsable --</option>';
while ($r = mysqli_fetch_assoc($resultado_resp)) {
    echo '      <option value="'.$r['id'].'">'.$r['nombre'].' '.$r['apellidos'].'</option>';
}
echo '      </select>
            <br><br>

            <label for="id_gato">Gato:</label>
            <select id="id_gato" name="id_gato" required>
                <option value="">-- Elige gato --</option>';
while ($g = mysqli_fetch_assoc($resultado_gatos)) {
    echo '      <option value="'.$g['id'].'">'.$g['id'].' - '.($g['num_chip'] ?: 'sin chip').' - '.substr($g['descripción'], 0, 40).'...</option>';
}
echo '      </select>
            <br><br>

            <label for="id_cementerio">Cementerio (opcional):</label>
            <select id="id_cementerio" name="id_cementerio">
                <option value="">-- Sin cementerio asignado todavía --</option>';
while ($c = mysqli_fetch_assoc($resultado_cem)) {
    echo '      <option value="'.$c['id'].'">'.$c['nombre'].'</option>';
}
echo '      </select>
            <br><br>

            <input type="hidden" name="Usuario" value="'.$usuario_log.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña_log.'">
            <input type="submit" value="Crear Solicitud">
        </form>
        <br>
        <form action="/BD2SQLITO/gatos/ver_solicitudes.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario_log.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña_log.'">
            <input type="submit" value="Ver Solicitudes">
        </form>
    </div>
</body>
</html>';

mysqli_close($con);
?>