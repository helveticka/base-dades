<?php
// ver_grupo_detalle.php - Muestra los detalles de un grupo de alimentación específico.

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

$usuario = isset($_GET['Usuario']) ? mysqli_real_escape_string($con, $_GET['Usuario']) : '';
$contraseña = isset($_GET['Contraseña']) ? mysqli_real_escape_string($con, $_GET['Contraseña']) : '';

$res_me = mysqli_query($con, "SELECT id FROM Persona WHERE usuario = '$usuario' AND contraseña = '$contraseña'");
if ($res_me && mysqli_num_rows($res_me) > 0) {
    $me = mysqli_fetch_assoc($res_me);
    $user_id = $me['id'];
} else {
    $user_id = "Desconocido";
}

$id_grupo = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_grupo === 0) {
    die("Error: ID de grupo no especificado.");
}
$consulta_grupo = "SELECT g.nombre AS nombre_grupo, p_resp.nombre AS nombre_responsable, p_resp.apellidos AS apellidos_responsable FROM Grupo g LEFT JOIN Persona p_resp ON p_resp.id_grupo = g.id AND p_resp.id_rol = 3 WHERE g.id = {$id_grupo}";
$resultado_grupo = mysqli_query($con, $consulta_grupo);
if (!$resultado_grupo) {
    die("Error en la consulta de grupo: " . mysqli_error($con));
}
$datos_grupo = mysqli_fetch_assoc($resultado_grupo);
if (!$datos_grupo) {
    die("Error: Grupo con ID {$id_grupo} no encontrado.");
}
$nombre_grupo = $datos_grupo['nombre_grupo'];
$nombre_responsable = $datos_grupo['nombre_responsable'] ? $datos_grupo['nombre_responsable'] . ' ' . $datos_grupo['apellidos_responsable'] : 'Sin responsable de grupo asignado directamente';
$consulta_voluntarios = "SELECT id, nombre, apellidos, id_rol, CASE WHEN id_rol = 3 THEN 'Responsable' WHEN id_rol = 2 THEN 'Voluntario' ELSE 'Otro' END AS tipo_rol FROM Persona WHERE id_grupo = {$id_grupo} AND (id_rol = 3 OR id_rol = 2) ORDER BY id_rol DESC, apellidos, nombre";
$resultado_voluntarios = mysqli_query($con, $consulta_voluntarios);

$html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Grupo: '.$nombre_grupo.'</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container wide">
        <h1>Detalle del Grupo: '.$nombre_grupo.' (ID Grupo: '.$id_grupo.', Mi ID: '.$user_id.')</h1>';
if (isset($_GET['msg'])) {
    $html .= "<div style='color:green; background-color:#e8f5e8; border: 1px solid green; padding: 10px; margin: 10px 0;'>" . $_GET['msg'] . "</div>";
}
$html .= '<h2>Información General</h2>
        <p><strong>Responsable Asignado:</strong> '.$nombre_responsable.'</p>
        <h2>Miembros del Grupo</h2>
        <div class="table-container">
            <table border="1">
                <tr>
                    <th>ID Persona</th>
                    <th>Nombre Completo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>';
if (mysqli_num_rows($resultado_voluntarios) > 0) {
    while ($reg_vol = mysqli_fetch_assoc($resultado_voluntarios)) {
        $html .= '<tr>';
        $html .= '<td>'.$reg_vol['id'].'</td>';
        $html .= '<td>'.$reg_vol['nombre'].' '.$reg_vol['apellidos'].'</td>';
        $html .= '<td>'.$reg_vol['tipo_rol'].'</td>';
        $html .= '<td><a href="/BD2SQLITO/grupos/quitar_miembros.php?id_persona='.$reg_vol['id'].'&id_grupo='.$id_grupo.'&Usuario='.$usuario.'&Contraseña='.$contraseña.'">Quitar del Grupo</a></td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="4">Este grupo no tiene miembros asignados actualmente.</td></tr>';
}
$html .= '</table>
        </div>
        <br>
        <h2>Gestión del Grupo</h2>
        <div class="flex-group">
            <form action="/BD2SQLITO/grupos/añadir_miembros.php" method="get">
                <input type="hidden" name="id_grupo" value="'.$id_grupo.'">
                <input type="hidden" name="Usuario" value="'.$usuario.'">
                <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                <input type="submit" value="Añadir Voluntarios">
            </form>
            <form action="/BD2SQLITO/grupos/ver_grupos.php" method="get">
                <input type="hidden" name="Usuario" value="'.$usuario.'">
                <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                <input type="submit" value="Lista de Grupos">
            </form>
            <form action="/BD2SQLITO/core/principal.php" method="get">
                <input type="hidden" name="Usuario" value="'.$usuario.'">
                <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                <input type="submit" value="Menú Principal" class="btn-secondary">
            </form>
        </div>
    </div>
</body>
</html>';
echo $html;

mysqli_close($con);
?>