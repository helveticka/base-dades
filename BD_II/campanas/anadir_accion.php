<?php
// anadir_accion.php - Añadir acción a una campaña
$id_campana = isset($_GET['id_campana']) ? $_GET['id_campana'] : '';
$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

$usuario = isset($_GET['Usuario']) ? mysqli_real_escape_string($con, $_GET['Usuario']) : '';
$contraseña = isset($_GET['Contraseña']) ? mysqli_real_escape_string($con, $_GET['Contraseña']) : '';



// OBTENCIÓN DINÁMICA DE ID DE USUARIO Y VALIDACIÓN
$res_me = mysqli_query($con, "SELECT id FROM Persona WHERE usuario = '$usuario' AND contraseña = '$contraseña'");
if ($res_me && mysqli_num_rows($res_me) > 0) {
    $me = mysqli_fetch_assoc($res_me);
    $user_id = $me['id'];
} else {
    $user_id = "Desconocido";
}

// Control de permisos (Añadir Acción)
$permiso_query = "SELECT COUNT(*) AS total
                  FROM Puede_hacer ph
                  JOIN Rol r ON r.id = ph.id_rol
                  JOIN Persona p ON p.id_rol = r.id
                  JOIN Privilegio pr ON pr.id = ph.id_privilegio
                  WHERE p.usuario = '$usuario' 
                  AND pr.titulo = 'Añadir Acción'";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $comentario = $_POST['comentario'];
    $id_gato = intval($_POST['id_gato']);
    $id_campana = intval($_POST['id_campana']);

    $insert_accion = "INSERT INTO Acción (fecha, comentario, id_campaña, id_gato)
                      VALUES ('$fecha', '$comentario', $id_campana, $id_gato)";
    mysqli_query($con, $insert_accion);
    $id_accion = mysqli_insert_id($con);

    if (!empty($_POST['veterinarios'])) {
        foreach ($_POST['veterinarios'] as $id_vet => $tipo) {
            if ($tipo !== "") {
                $insert_part = "INSERT INTO Participación (id_acción, id_veterinario, tipo)
                                VALUES ($id_accion, $id_vet, '$tipo')";
                mysqli_query($con, $insert_part);
            }
        }
    }
    $success = "Acción añadida correctamente.";
}

// Cargar datos para el formulario
$gatos_result = mysqli_query($con, "SELECT id, num_chip, descripción FROM Gato");
$veterinarios_result = mysqli_query($con, "SELECT id, nombre, apellidos FROM Persona WHERE id_rol = 4");

echo '<html>
<head>
    <meta charset="utf-8">
    <title>Añadir Acción</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Añadir Acción a la Campaña '.$id_campana.' (Mi ID: '.$user_id.')</h1>';

if (isset($success)) echo '<p style="color:green;">'.$success.'</p>';

echo '  <form method="POST">
            <label for="fecha">Fecha de la acción:</label>
            <input type="datetime-local" id="fecha" name="fecha" required>
            <br><br>

            <label for="comentario">Comentario:</label>
            <textarea id="comentario" name="comentario" rows="3"></textarea>
            <br><br>

            <label for="id_gato">Gato intervenido:</label>
            <select id="id_gato" name="id_gato" required>
                <option value="">Seleccione un gato</option>';
while ($g = mysqli_fetch_assoc($gatos_result)) {
    echo '      <option value="'.$g['id'].'">'.$g['descripción'].' ('.($g['num_chip'] ?: "Sin chip").')</option>';
}
echo '      </select>
            <br><br>

            <h3>Profesionales participantes</h3>
            <p>Indica el rol de cada profesional que participa (opcional):</p>';

while ($v = mysqli_fetch_assoc($veterinarios_result)) {
    echo '  <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                <strong>'.$v['nombre'].' '.$v['apellidos'].'</strong><br>
                <select name="veterinarios['.$v['id'].']">
                    <option value="">-- No participa --</option>
                    <option value="Manescal">Veterinario</option>
                    <option value="Auxiliar">Auxiliar</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>';
}

echo '      <input type="hidden" name="id_campana" value="'.$id_campana.'">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Añadir Acción">
        </form>
        <br>
        <form action="/BD2SQLITO/campanas/ver_campana.php" method="get">
            <input type="hidden" name="id" value="'.$id_campana.'">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Volver a la Campaña">
        </form>
    </div>
</body>
</html>';

mysqli_close($con);
?>
