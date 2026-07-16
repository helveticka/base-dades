<?php
// añadir_miembros.php - Gestiona la asignación de Interesados/Voluntarios a un grupo.

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

$rol_voluntario = 2;
$rol_interesado = 1;
$rol_responsable = 3;
$mensaje = "";
$id_grupo_seleccionado = isset($_GET['id_grupo']) ? intval($_GET['id_grupo']) : 0;
$id_interesado_a_procesar = isset($_GET['id_interesado']) ? intval($_GET['id_interesado']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_persona']) && isset($_POST['id_grupo'])) {
    $id_persona = intval($_POST['id_persona']);
    $id_grupo = intval($_POST['id_grupo']);
    $es_responsable = isset($_POST['es_responsable']) ? 1 : 0;
    $consulta_info = "SELECT id_rol, nombre, apellidos, usuario FROM Persona WHERE id = {$id_persona}";
    $res_info = mysqli_query($con, $consulta_info);
    $info_persona = mysqli_fetch_assoc($res_info);
    if (!$info_persona) {
        $mensaje = "<div style='color:red;'> Error: Persona no encontrada.</div>";
    } else {
        $rol_actual = intval($info_persona['id_rol']);
        $updates = [];
        $feedback = [];
        $nuevo_rol = $rol_voluntario;
        if ($es_responsable == 1) {
            $nuevo_rol = $rol_responsable;
        }
        if ($rol_actual === $rol_interesado) {
            $updates[] = "id_rol = {$nuevo_rol}";
            $feedback[] = "Rol actualizado de Interesado a " . ($nuevo_rol === $rol_responsable ? 'Responsable' : 'Voluntario');
        } elseif ($rol_actual !== $nuevo_rol) {
            $updates[] = "id_rol = {$nuevo_rol}";
            $feedback[] = "Rol de la persona actualizado a " . ($nuevo_rol === $rol_responsable ? 'Responsable' : 'Voluntario');
        }
        $updates[] = "id_grupo = {$id_grupo}";
        $feedback[] = "Asignado al Grupo ID {$id_grupo}";
        if (!empty($updates)) {
            $set_clause = implode(', ', $updates);
            $consulta_update = "UPDATE Persona SET {$set_clause} WHERE id = {$id_persona}";
            if (mysqli_query($con, $consulta_update)) {
                header("Location: /BD2SQLITO/grupos/ver_grupo_detalle.php?id={$id_grupo}&Usuario={$usuario}&Contraseña={$contraseña}");
                exit;
            } else {
                $mensaje = "<div style='color:red;'>Error al actualizar la persona: " . mysqli_error($con) . "</div>";
            }
        } else {
            $mensaje = "<div style='color:blue;'>ℹNo se realizaron cambios.</div>";
        }
    }
}

$nombre_grupo_actual = "Seleccione un Grupo";
if ($id_grupo_seleccionado > 0) {
    $consulta_nombre_grupo = "SELECT nombre FROM Grupo WHERE id = {$id_grupo_seleccionado}";
    $res_nombre = mysqli_query($con, $consulta_nombre_grupo);
    if ($fila_nombre = mysqli_fetch_assoc($res_nombre)) {
        $nombre_grupo_actual = $fila_nombre['nombre'];
    }
}
$consulta_grupos = "SELECT id, nombre FROM Grupo WHERE id_ayuntamiento = {$ayuntamiento_id} ORDER BY nombre";
$grupos = mysqli_query($con, $consulta_grupos);
$consulta_personas = "SELECT id, nombre, apellidos, id_rol FROM Persona WHERE id_ayuntamiento = {$ayuntamiento_id} AND (id_rol = {$rol_interesado} OR (id_rol IN ({$rol_voluntario}, {$rol_responsable}) AND id_grupo IS NULL)) ORDER BY id_rol DESC, apellidos, nombre";
$personas = mysqli_query($con, $consulta_personas);

$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Añadir Miembro a Grupo</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Asignar Miembro a Grupo (Ayto ID: '.$ayuntamiento_id.', Mi ID: '.$user_id.')</h1>
        <p>Grupo de Destino: <strong>'.$nombre_grupo_actual.'</strong></p>';
if ($mensaje !== "") {
    $html .= $mensaje;
}
if (isset($_SESSION['msg'])) {
    $html .= "<div style='color:green; border: 1px solid green; padding: 10px;'>".htmlspecialchars($_SESSION['msg'])."</div>";
    unset($_SESSION['msg']);
} elseif (isset($_GET['msg'])) {
    $html .= "<div style='color:green; border: 1px solid green; padding: 10px;'>".htmlspecialchars($_GET['msg'])."</div>";
}
$html .= '<form method="POST" action="/BD2SQLITO/grupos/añadir_miembros.php">
            <label for="id_persona">Seleccionar Persona:</label>
            <select id="id_persona" name="id_persona" required>
                <option value="">-- Seleccione un Interesado o Voluntario sin grupo --</option>';
if (mysqli_num_rows($personas) > 0) {
    mysqli_data_seek($personas, 0);
    while ($reg = mysqli_fetch_assoc($personas)) {
        $rol_etiqueta = ($reg['id_rol'] == $rol_interesado) ? 'INTERESADO (Pendiente de promoción)' : (($reg['id_rol'] == $rol_responsable) ? 'RESPONSABLE sin grupo' : 'VOLUNTARIO sin grupo');
        $selected = ($reg['id'] == $id_interesado_a_procesar) ? 'selected' : '';
        $html .= '<option value="'.$reg['id'].'" '.$selected.'>'.$reg['nombre'].' '.$reg['apellidos'].' ('.$rol_etiqueta.')</option>';
    }
} else {
    $html .= '<option value="" disabled>No hay personas disponibles para asignar.</option>';
}
$html .= '</select>
            <br><br>
            <label for="id_grupo">Asignar a Grupo:</label>
            <select id="id_grupo" name="id_grupo" required>
                <option value="">-- Seleccione el Grupo --</option>';
if (mysqli_num_rows($grupos) > 0) {
    mysqli_data_seek($grupos, 0);
    while ($reg_g = mysqli_fetch_assoc($grupos)) {
        $selected = ($reg_g['id'] == $id_grupo_seleccionado) ? 'selected' : '';
        $html .= '<option value="'.$reg_g['id'].'" '.$selected.'>'.$reg_g['nombre'].' (ID: '.$reg_g['id'].')</option>';
    }
}
$html .= '</select>
            <br><br>
            <label><input type="checkbox" name="es_responsable" value="1"> Asignar como Responsable (Esto cambiará su Rol a \'Responsable\' y lo asignará al grupo).</label>
            <br><br>
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <button type="submit">Asignar y Actualizar Rol</button>
        </form>
        <br>
        <form action="/BD2SQLITO/grupos/ver_grupos.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Volver a la Lista de Grupos">
        </form>
    </div>
</body>
</html>';
echo $html;

mysqli_close($con);
?>