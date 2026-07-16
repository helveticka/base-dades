<?php
// anadir_vet.php - Permite al administrador del centro registrar un nuevo veterinario.

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

$usuario = isset($_GET['Usuario']) ? mysqli_real_escape_string($con, $_GET['Usuario']) : (isset($_POST['Usuario']) ? mysqli_real_escape_string($con, $_POST['Usuario']) : '');
$contraseña = isset($_GET['Contraseña']) ? mysqli_real_escape_string($con, $_GET['Contraseña']) : (isset($_POST['Contraseña']) ? mysqli_real_escape_string($con, $_POST['Contraseña']) : '');

$res_me = mysqli_query($con, "SELECT id, id_centro_veterinario FROM Persona WHERE usuario = '$usuario' AND contraseña = '$contraseña'");
if ($res_me && mysqli_num_rows($res_me) > 0) {
    $me = mysqli_fetch_assoc($res_me);
    $user_id = $me['id'];
    $centro_id = $me['id_centro_veterinario'];
} else {
    die("Error: Sesión no válida o usuario no encontrado.");
}

if (!$centro_id) {
    die("Error: Este usuario no tiene permisos para añadir veterinarios.");
}

$rol_veterinario = 4;
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre_vet'])) {
    $nombre = trim($_POST['nombre_vet']);
    $apellidos = trim($_POST['apellidos_vet']);
    $usuario_vet = trim($_POST['usuario_vet']);
    $pass_vet = trim($_POST['pass_vet']);

    if (empty($nombre) || empty($apellidos) || empty($usuario_vet) || empty($pass_vet)) {
        $mensaje = "<div style='color:red;'>❌ Todos los campos son obligatorios.</div>";
    } else {
        $nombre_s = mysqli_real_escape_string($con, $nombre);
        $apellidos_s = mysqli_real_escape_string($con, $apellidos);
        $usuario_s = mysqli_real_escape_string($con, $usuario_vet);
        $pass_s = mysqli_real_escape_string($con, $pass_vet);

        $consulta_insert = "INSERT INTO Persona (nombre, apellidos, usuario, contraseña, id_rol, id_centro_veterinario) 
                            VALUES ('$nombre_s', '$apellidos_s', '$usuario_s', '$pass_s', $rol_veterinario, $centro_id)";
        
        if (mysqli_query($con, $consulta_insert)) {
            $mensaje = "<div style='color:green;'>✅ Veterinario registrado con éxito.</div>";
        } else {
            $mensaje = "<div style='color:red;'>❌ Error al registrar: " . mysqli_error($con) . "</div>";
        }
    }
}

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Añadir Veterinario</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Registrar Nuevo Veterinario (Centro ID: '.$centro_id.', Mi ID: '.$user_id.')</h1>';

if ($mensaje !== "") {
    $html .= $mensaje;
}

$html .= '<form method="POST" action="/BD2SQLITO/grupos/anadir_vet.php">
            <label for="nombre_vet">Nombre:</label><br>
            <input type="text" id="nombre_vet" name="nombre_vet" required><br><br>
            
            <label for="apellidos_vet">Apellidos:</label><br>
            <input type="text" id="apellidos_vet" name="apellidos_vet" required><br><br>
            
            <label for="usuario_vet">Usuario de acceso:</label><br>
            <input type="text" id="usuario_vet" name="usuario_vet" required><br><br>
            
            <label for="pass_vet">Contraseña:</label><br>
            <input type="password" id="pass_vet" name="pass_vet" required><br><br>
            
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <button type="submit">Registrar Veterinario</button>
        </form>
        <br>
        <form action="/BD2SQLITO/grupos/ver_veterinarios.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Volver al Listado">
        </form>
    </div>
</body>
</html>';

echo $html;
mysqli_close($con);
?>
