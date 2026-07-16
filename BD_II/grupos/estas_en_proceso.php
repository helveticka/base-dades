<?php
// estas_en_proceso.php - Pantalla que ve el usuario con rol 'Interesado' (Rol 1)
// Muestra el estado de su postulación y el ayuntamiento responsable.

// Configuración de la conexión y la base de datos
$con = mysqli_connect("localhost", "root", "");
$db_name = "BD2SQLITO";
$rol_interesado = 1; 

if (!$con) {
    die("Error al conectar con MySQL: " . mysqli_connect_error());
}

$db = mysqli_select_db($con, $db_name);

if (!$db) {
    die("Error al seleccionar la base de datos '{$db_name}': " . mysqli_error($con));
}

// --- OBTENCIÓN DINÁMICA DE DATOS DE USUARIO ---
$usuario = isset($_GET['Usuario']) ? mysqli_real_escape_string($con, $_GET['Usuario']) : '';
$contraseña = isset($_GET['Contraseña']) ? mysqli_real_escape_string($con, $_GET['Contraseña']) : '';

$res_user = mysqli_query($con, "SELECT id FROM Persona WHERE usuario = '$usuario' AND contraseña = '$contraseña'");
if ($res_user && mysqli_num_rows($res_user) > 0) {
    $user_row = mysqli_fetch_assoc($res_user);
    $id_usuario_actual = $user_row['id'];
} else {
    die("Error: Sesión no válida o usuario no encontrado.");
}

// Consulta para obtener los datos del usuario y el nombre de su Ayuntamiento
$consulta_datos_usuario = "
    SELECT 
        P.nombre, 
        P.apellidos, 
        P.id_rol,
        A.nombre AS nombre_ayuntamiento
    FROM Persona P
    JOIN Ayuntamiento A ON P.id_ayuntamiento = A.id
    WHERE P.id = {$id_usuario_actual}
";

$resultado_usuario = mysqli_query($con, $consulta_datos_usuario);

if (!$resultado_usuario || mysqli_num_rows($resultado_usuario) == 0) {
    // Si el usuario no existe o la consulta falla (caso inusual)
    die("Error: No se pudieron cargar los datos del usuario o ID no válido.");
}

$datos_usuario = mysqli_fetch_assoc($resultado_usuario);

$nombre_completo = htmlspecialchars($datos_usuario['nombre'] . ' ' . $datos_usuario['apellidos']);
$nombre_ayuntamiento = htmlspecialchars($datos_usuario['nombre_ayuntamiento']);

// --- GENERACIÓN DEL HTML ---
$html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso de Selección</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="auth-layout">
        <div class="form-container">
            <div class="badge">En proceso</div>

            <h2>¡Hola, '.$nombre_completo.'!</h2>
            <p class="welcome-text" style="margin-top:-10px;">ID: '.$id_usuario_actual.'</p>

            <h1>En Proceso de Selección</h1>

            <div class="main-card" style="text-align:left;">
                <h3 style="margin-top:0;">Postulación recibida ✅</h3>
                <p style="margin:0.75rem 0 0;">
                    Tu postulación como Interesado ha sido recibida correctamente.
                </p>
                <p style="margin:0.75rem 0 0;">
                    Actualmente te encuentras en proceso de selección y asignación de grupo por parte del equipo de gestión de colonias felinas.
                </p>
            </div>

            <p style="margin-top:1.25rem; line-height:1.6;">
                El Ayuntamiento de <strong>'.$nombre_ayuntamiento.'</strong> te avisará cuando seas asignado a un grupo de voluntariado o se te proporcionen tus credenciales.
            </p>

            <p style="margin-top:1rem; color: var(--text-muted);">
                Gracias por tu interés en ayudar a las colonias felinas.
            </p>

            <hr style="margin:1.75rem 0; border:0; border-top:1px solid var(--border-color);">

            <form action="/BD2SQLITO/core/login.html" method="get">
                <input type="submit" value="Cerrar sesión" class="btn btn-danger">
            </form>
        </div>
    </div>
</body>
</html>';


echo $html;

// Cerrar conexión
mysqli_close($con);
?>