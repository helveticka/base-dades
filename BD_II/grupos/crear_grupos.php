<?php
// crear_grupos.php - Permite al Responsable crear un nuevo grupo de alimentación.

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

$usuario = isset($_GET['Usuario']) ? mysqli_real_escape_string($con, $_GET['Usuario']) : (isset($_POST['Usuario']) ? mysqli_real_escape_string($con, $_POST['Usuario']) : '');
$contraseña = isset($_GET['Contraseña']) ? mysqli_real_escape_string($con, $_GET['Contraseña']) : (isset($_POST['Contraseña']) ? mysqli_real_escape_string($con, $_POST['Contraseña']) : '');

$res_me = mysqli_query($con, "SELECT id, id_ayuntamiento FROM Persona WHERE usuario = '$usuario' AND contraseña = '$contraseña'");
if ($res_me && mysqli_num_rows($res_me) > 0) {
    $me = mysqli_fetch_assoc($res_me);
    $user_id = $me['id'];
    $ayuntamiento_id = $me['id_ayuntamiento'];
} else {
    die("Error: Sesión no válida o usuario no encontrado.");
}

$mensaje = "";

if (isset($_GET['nombre_grupo'])) {
    $nombre_grupo = trim($_GET['nombre_grupo']);
    if ($nombre_grupo === "") {
        $mensaje = '<div style="color:red;">❌ El nombre del grupo no puede estar vacío.</div>';
    } else {
        $nombre_grupo_seguro = mysqli_real_escape_string($con, $nombre_grupo);
        $consulta_check = "SELECT id FROM Grupo WHERE nombre = '$nombre_grupo_seguro' AND id_ayuntamiento = $ayuntamiento_id";
        $resultado_check = mysqli_query($con, $consulta_check);
        if (mysqli_num_rows($resultado_check) > 0) {
            $mensaje = '<div style="color:orange;">Ya existe un grupo con ese nombre en este ayuntamiento.</div>';
        } else {
            $consulta_insert = "INSERT INTO Grupo (nombre, id_ayuntamiento) VALUES ('$nombre_grupo_seguro', $ayuntamiento_id)";
            if (mysqli_query($con, $consulta_insert)) {
                $mensaje = '<div style="color:green;">Grupo creado con éxito.</div>';
                $nombre_grupo = "";
            } else {
                $mensaje = '<div style="color:red;">Error al crear el grupo: '.mysqli_error($con).'</div>';
            }
        }
    }
}

echo '<html>
<head>
    <meta charset="utf-8">
    <title>Crear Grupo</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Crear Grupo (Ayto ID: '.$ayuntamiento_id.', Mi ID: '.$user_id.')</h1>
        <form action="/BD2SQLITO/grupos/crear_grupos.php" method="get">
            <label for="nombre_grupo">Nombre del Grupo:</label>
            <input type="text" id="nombre_grupo" name="nombre_grupo" value="'.(isset($nombre_grupo) ? $nombre_grupo : '').'" required maxlength="100" placeholder="Ej: Grupo de Mañana 1">
            <br><br>
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Crear Grupo">
        </form>';
if ($mensaje !== "") {
    echo '<br>'.$mensaje;
}
echo '    <br>
        <form action="/BD2SQLITO/grupos/ver_grupos.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Volver a la Lista de Grupos">
        </form>
    </div>
</body>
</html>';

mysqli_close($con);
?>