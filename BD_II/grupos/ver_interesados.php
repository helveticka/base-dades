<?php
// ver_interesados.php - Muestra una lista de las personas interesadas en ser voluntarios (Rol 1)
// El Responsable del ayuntamiento puede gestionar su promoción a Voluntario.

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

$usuario = isset($_GET['Usuario']) ? mysqli_real_escape_string($con, $_GET['Usuario']) : '';
$contraseña = isset($_GET['Contraseña']) ? mysqli_real_escape_string($con, $_GET['Contraseña']) : '';

$res_me = mysqli_query($con, "SELECT id, id_ayuntamiento FROM Persona WHERE usuario = '$usuario' AND contraseña = '$contraseña'");
if ($res_me && mysqli_num_rows($res_me) > 0) {
    $me = mysqli_fetch_assoc($res_me);
    $user_id = $me['id'];
    $ayuntamiento_id = $me['id_ayuntamiento'];
} else {
    die("Error: Sesión no válida o usuario no encontrado.");
}
$rol_interesado = 1;

$consulta = "SELECT id, nombre, apellidos FROM Persona WHERE id_rol = $rol_interesado AND id_ayuntamiento = $ayuntamiento_id ORDER BY apellidos, nombre";
$result = mysqli_query($con, $consulta);

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ver Interesados</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Listado de Interesados (Ayto ID: '.$ayuntamiento_id.', Mi ID: '.$user_id.')</h1>
        <table border="1">
            <tr><th>ID Persona</th><th>Nombre Completo</th><th>Acciones</th></tr>';
if (mysqli_num_rows($result) > 0) {
    while ($reg = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td>'.$reg['id'].'</td>';
        $html .= '<td>'.$reg['nombre'].' '.$reg['apellidos'].'</td>';
        $html .= '<td><a href="/BD2SQLITO/grupos/añadir_miembros.php?id_interesado='.$reg['id'].'&Usuario='.$usuario.'&Contraseña='.$contraseña.'">Convertir a Voluntario</a></td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="3">No hay personas interesadas registradas para este ayuntamiento.</td></tr>';
}
$html .= '</table>
        <br>
        <form action="/BD2SQLITO/grupos/ver_grupos.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Volver a la Lista de Grupos">
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