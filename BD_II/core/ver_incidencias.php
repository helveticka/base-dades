<?php
// ver_incidencias.php - Incidencias con sidebar unificado
$id_grupo = $_GET['id_grupo'];
$usuario = $_GET['Usuario'];
$contraseña = $_GET['Contraseña'];

// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Obtener todas las visitas del grupo
$visitas_query = "SELECT v.fecha,v.comentario,
                         p.nombre AS responsable,
                         c.descripción AS colonia
                  FROM visita v
                  JOIN persona p 
                  ON v.id_responsable = p.id
                  JOIN colonia c 
                  ON v.id_colonia = c.id
                  JOIN grupo g 
                  ON g.id_ayuntamiento = c.id_ayuntamiento
                  AND g.id = $id_grupo
                  ORDER BY v.fecha DESC";
$visitas_result = mysqli_query($con, $visitas_query);

// Obtener permisos para la barra lateral
$consulta_perm = "SELECT titulo, 
                         enlace
                  FROM persona
                  JOIN rol 
                  ON persona.id_rol = rol.id
                  JOIN puede_hacer
                  ON puede_hacer.id_rol = rol.id
                  JOIN privilegio 
                  ON privilegio.id = puede_hacer.id_privilegio
                  WHERE persona.usuario = '$usuario'";
$res_perm = mysqli_query($con, $consulta_perm);

// Generar HTML dinámicamente con echo
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Incidencias | Sqlito</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-nav">
                <a href="/BD2SQLITO/core/principal.php?Usuario='.$usuario.'&Contraseña='.$contraseña.'" class="sidebar-logo">SQLITO</a>';

while ($reg_p = mysqli_fetch_assoc($res_perm)) {
    $enlace = $reg_p["enlace"];
    $extra = (strpos($enlace, 'id_grupo') === false) ? "?id_grupo=$id_grupo&" : "?";
    echo '<a href="'.$enlace.$extra.'Usuario='.$usuario.'&Contraseña='.$contraseña.'">
            <span>'.$reg_p["titulo"].'</span>
          </a>';
}

echo '            </div>
            <div class="sidebar-footer">
                <form action="login.html">
                    <input type="submit" value="Cerrar sesión" class="btn-danger">
                </form>
            </div>
        </div>

        <div class="main-content">
            <h1>Incidencias Registradas</h1>
            <p class="welcome-text">Historial de visitas y reportes de seguridad para las colonias del grupo.</p>

            <div class="incidents-list">';

if (mysqli_num_rows($visitas_result) > 0) {
    $fecha_actual = null;
    while ($visita = mysqli_fetch_assoc($visitas_result)) {
        $fecha_visita = date('Y-m-d', strtotime($visita['fecha']));
        if ($fecha_actual !== $fecha_visita) {
            if ($fecha_actual !== null) echo '</div>'; // Cerrar grupo anterior
            $fecha_actual = $fecha_visita;
            echo '<div class="incident-group">';
            echo '<div class="incident-date">📅 ' . date('l, d M Y', strtotime($fecha_actual)) . '</div>';
        }
        echo '<div class="incident-card">
                <div class="incident-header">
                    <div>
                        <div style="font-weight: 700; color: var(--primary); margin-bottom: 0.25rem;">
                            📍 '.$visita['colonia'].'
                        </div>
                        <div class="incident-meta">
                            <span>👤 '.$visita['responsable'].'</span>
                            <span>🕒 '.date('H:i', strtotime($visita['fecha'])).'</span>
                        </div>
                    </div>
                    <span class="badge">Reporte</span>
                </div>
                <div class="incident-body">
                    📝 '.$visita['comentario'].'
                </div>
              </div>';
    }
    echo '</div>'; // Cerrar último grupo
} else {
    echo '<div class="main-card" style="text-align: center; padding: 5rem;">
            <span style="font-size: 3rem; display: block; margin-bottom: 1rem;">📋</span>
            <h3 style="color: var(--text-main);">Sin incidencias</h3>
            <p style="color: var(--text-muted);">No se han registrado visitas o reportes aún.</p>
          </div>';
}

echo '            </div>

            <div style="margin-top: 2rem;">
                <a href="/BD2SQLITO/core/principal.php?Usuario='.$usuario.'&Contraseña='.$contraseña.'" class="btn btn-secondary" style="width: auto;">
                    Volver al panel principal
                </a>
            </div>
        </div>
    </div>
</body>
</html>';

mysqli_close($con);
?>