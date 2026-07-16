<?php
// registrar_manada.php - registro de nuevas manadas en el ayuntamiento de la persona loggeada
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

//consulta para seleccionar el ayuntamiento del usuario que ha accedido
$q_ayunt = "SELECT id_ayuntamiento
            FROM Persona
            WHERE usuario = '$usuario' AND contraseña = '$contraseña'
            LIMIT 1";

$r_user = mysqli_query($con, $q_ayunt);
if (!$r_user) {
    die("Error en consulta de ayuntamiento: " . mysqli_error($con) . "<br>SQL: " . $q_ayunt);
}
if (mysqli_num_rows($r_user) == 0) {
    die("Error: usuario/contraseña inválidos o no encontrados.");
}

$u = mysqli_fetch_assoc($r_user);
$id_ayuntamiento = (int)$u['id_ayuntamiento'];

// si se quiere registrar 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitud = mysqli_real_escape_string($con, $_POST['latitud'] ?? '');
    $longitud = mysqli_real_escape_string($con, $_POST['longitud'] ?? '');
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion'] ?? '');

    // validación de los datos
    if ($latitud !== '' && $longitud !== '' && $descripcion !== '' && $id_ayuntamiento > 0) {
        $insert_colonia = "INSERT INTO Colonia (latitud, longitud, `descripción`, id_ayuntamiento)
                           VALUES ('$latitud', '$longitud', '$descripcion', $id_ayuntamiento)";

        $res_ins = mysqli_query($con, $insert_colonia);
        if ($res_ins) {
            $mensaje = "Manada creada correctamente. ID: " . mysqli_insert_id($con);
        } else {
            $mensaje = "Error al crear manada: " . mysqli_error($con) . "<br>SQL: " . $insert_colonia;
        }
    } else {
        $mensaje = "Faltan datos obligatorios.";
    }
}

//html de la página
echo '<html>
<head>
    <meta charset="utf-8">
    <title>Registrar Manada</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Registrar Nueva Manada (Colonia)</h1>';

if (isset($mensaje)) echo '<p style="color:blue;">'.$mensaje.'</p>';

echo '  <form method="post">
            <label for="latitud">Latitud:</label>
            <input type="text" id="latitud" name="latitud" required placeholder="39.56960000">
            <br><br>

            <label for="longitud">Longitud:</label>
            <input type="text" id="longitud" name="longitud" required placeholder="2.65020000">
            <br><br>

            <label for="descripcion">Descripción de la manada / colonia:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
            <br><br>

            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Crear Manada">
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

mysqli_close($con);
?>
