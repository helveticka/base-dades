<?php
// ver_grupos.php - Muestra una lista básica de los nombres de los grupos de alimentación.

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

$consulta = "SELECT g.id, g.nombre AS nombre_grupo FROM Grupo g WHERE g.id_ayuntamiento = {$ayuntamiento_id} ORDER BY g.nombre";
$resultat = mysqli_query($con, $consulta);

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ver Grupos</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Listado de Grupos (Ayto ID: '.$ayuntamiento_id.', Mi ID: '.$user_id.')</h1>
        <ul>';
if (mysqli_num_rows($resultat) > 0) {
    while ($reg = mysqli_fetch_assoc($resultat)) {
        $html .= '<li><a href="/BD2SQLITO/grupos/ver_grupo_detalle.php?id='.$reg['id'].'&Usuario='.$usuario.'&Contraseña='.$contraseña.'">'.$reg['nombre_grupo'].' (ID: '.$reg['id'].')</a></li>';
    }
} else {
    $html .= '<li>No hay grupos registrados para este ayuntamiento.</li>';
}
$html .= '</ul>
        <br>
        <form action="/BD2SQLITO/grupos/crear_grupos.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Crear Nuevo Grupo">
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