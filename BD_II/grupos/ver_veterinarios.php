<?php
// ver_veterinarios.php - Muestra una lista de los veterinarios asociados a un centro (Rol 4)
// Solo accesible para el administrador del Centro Veterinario (Rol 6).

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

$usuario = isset($_GET['Usuario']) ? mysqli_real_escape_string($con, $_GET['Usuario']) : '';
$contraseña = isset($_GET['Contraseña']) ? mysqli_real_escape_string($con, $_GET['Contraseña']) : '';

$res_me = mysqli_query($con, "SELECT id, id_centro_veterinario FROM Persona WHERE usuario = '$usuario' AND contraseña = '$contraseña'");
if ($res_me && mysqli_num_rows($res_me) > 0) {
    $me = mysqli_fetch_assoc($res_me);
    $user_id = $me['id'];
    $centro_id = $me['id_centro_veterinario'];
} else {
    die("Error: Sesión no válida o usuario no encontrado.");
}

if (!$centro_id) {
    die("Error: Este usuario no está asociado a ningún centro veterinario.");
}

$rol_veterinario = 4;

$consulta = "SELECT id, nombre, apellidos, usuario FROM Persona WHERE id_rol = $rol_veterinario AND id_centro_veterinario = $centro_id ORDER BY apellidos, nombre";
$result = mysqli_query($con, $consulta);

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Veterinarios del Centro</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Listado de Veterinarios (Centro ID: '.$centro_id.', Mi ID: '.$user_id.')</h1>
        <table border="1">
            <tr><th>ID Persona</th><th>Nombre Completo</th><th>Usuario</th></tr>';
if (mysqli_num_rows($result) > 0) {
    while ($reg = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td>'.$reg['id'].'</td>';
        $html .= '<td>'.$reg['nombre'].' '.$reg['apellidos'].'</td>';
        $html .= '<td>'.$reg['usuario'].'</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="3">No hay veterinarios registrados para este centro.</td></tr>';
}
$html .= '</table>
        <br>
        <form action="/BD2SQLITO/grupos/anadir_vet.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Añadir Nuevo Veterinario">
        </form>
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
