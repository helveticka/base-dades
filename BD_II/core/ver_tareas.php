<?php
// ver_tareas.php - Calendario de suministros con sidebar unificado
$id_grupo = $_GET['id_grupo'];
$usuario = $_GET['Usuario'];
$contraseña = $_GET['Contraseña'];

// Obtener la semana actual o la semana seleccionada
$semana_actual = isset($_GET['semana']) ? $_GET['semana'] : date('Y-m-d', strtotime('monday this week'));

// Calcular el inicio de la semana anterior y la semana siguiente
$semana_anterior = date('Y-m-d', strtotime('-7 days', strtotime($semana_actual)));
$semana_siguiente = date('Y-m-d', strtotime('+7 days', strtotime($semana_actual)));

// Calcular el inicio y fin de la semana actual
$inicio_semana = date('Y-m-d', strtotime('monday this week', strtotime($semana_actual)));
$fin_semana = date('Y-m-d', strtotime('sunday this week', strtotime($semana_actual)));

// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Obtener el rol del usuario
$rol_query = "SELECT id_rol FROM persona WHERE usuario = '$usuario'";
$rol_result = mysqli_query($con, $rol_query);
$rol_row = mysqli_fetch_assoc($rol_result);
$id_rol = $rol_row['id_rol'];

// Ajustar la consulta según el rol
if ($id_rol == 3) { // Responsable (puede ver todas las tareas del grupo)
    $suministros_query = "SELECT s.id,
                                 s.descripción,
                                 s.fecha, s.cantidad, 
                                 c.descripción AS colonia, 
                                 CONCAT(p.nombre, ' ', p.apellidos) AS voluntario,
                                 m.nombre AS marca
                           FROM subministrament s
                           JOIN colonia c 
                           ON s.id_colonia = c.id
                           JOIN persona p 
                           ON s.id_voluntario = p.id 
                           AND p.id_grupo = $id_grupo
                           JOIN marca m 
                           ON s.id_marca = m.id
                           WHERE DATE(s.fecha) BETWEEN '$inicio_semana' AND '$fin_semana'
                           ORDER BY s.fecha";
} else if ($id_rol == 2) { // Voluntario (solo puede ver sus propias tareas)
    $suministros_query = "SELECT s.id,
                                 s.descripción,
                                 s.fecha,
                                 s.cantidad,
                                 c.descripción AS colonia, 
                                 CONCAT(p.nombre, ' ', p.apellidos) AS voluntario,
                                 m.nombre AS marca
                          FROM subministrament s
                          JOIN colonia c 
                          ON s.id_colonia = c.id
                          JOIN persona p 
                          ON s.id_voluntario = p.id 
                          AND p.usuario = '$usuario'
                          JOIN marca m 
                          ON s.id_marca = m.id
                          WHERE DATE(s.fecha) BETWEEN '$inicio_semana' AND '$fin_semana'
                          ORDER BY s.fecha";
}
$suministros_result = mysqli_query($con, $suministros_query);

// Crear un array para organizar los suministros por día
$suministros_por_dia = [];
while ($suministro = mysqli_fetch_assoc($suministros_result)) {
    $dia = date('Y-m-d', strtotime($suministro['fecha'])); // Obtener solo la fecha (sin la hora)
    $hora = date('H:i', strtotime($suministro['fecha'])); // Obtener solo la hora
    $suministros_por_dia[$dia][] = [
        'hora' => $hora,
        'descripcion' => $suministro['descripción'],
        'cantidad' => $suministro['cantidad'],
        'colonia' => $suministro['colonia'],
        'voluntario' => $suministro['voluntario'],
        'marca' => $suministro['marca']
    ];
}

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
echo '<html>
<head>
    <meta charset="utf-8">
    <title>Ver Suministros | Sqlito</title>
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
            <h1>Agenda de Suministros</h1>
            <p class="welcome-text">Seguimiento semanal de alimentación y suministros para tu grupo.</p>

            <div class="main-card" style="display:flex; justify-content: space-between; align-items: center; padding: 1.25rem 2rem;">
                <a href="/BD2SQLITO/core/ver_tareas.php?id_grupo='.$id_grupo.'&Usuario='.$usuario.'&Contraseña='.$contraseña.'&semana='.$semana_anterior.'" class="btn btn-secondary" style="width: auto;">
                    ← Anterior
                </a>
                <div style="text-align: center;">
                    <span style="font-weight: 600; color: var(--text-main);">
                        '.date('d M', strtotime($inicio_semana)).' — '.date('d M', strtotime($fin_semana)).'
                    </span>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Semana actual</div>
                </div>
                <a href="/BD2SQLITO/core/ver_tareas.php?id_grupo='.$id_grupo.'&Usuario='.$usuario.'&Contraseña='.$contraseña.'&semana='.$semana_siguiente.'" class="btn btn-secondary" style="width: auto;">
                    Siguiente →
                </a>
            </div>

            <div class="calendar-grid">';

for ($i = 0; $i < 7; $i++) {
    $dia = date('Y-m-d', strtotime("+$i day", strtotime($inicio_semana)));
    $isToday = ($dia == date('Y-m-d')) ? 'style="border-color: var(--primary); transform: scale(1.02);"' : '';
    echo '<div class="day-column" '.$isToday.'>';
    echo '<h2>' . date('D, d', strtotime($dia)) . ' <span style="font-size: 0.75rem; opacity: 0.6;">' . date('M', strtotime($dia)) . '</span></h2>';

    if (isset($suministros_por_dia[$dia])) {
        foreach ($suministros_por_dia[$dia] as $suministro) {
            echo '<div class="task-card">';
            echo '<div class="time">🕒 ' . $suministro['hora'] . '</div>';
            echo '<p><strong>📦</strong> ' . $suministro['descripcion'] . ' (' . $suministro['cantidad'] . ' kg)</p>';
            echo '<p><strong>📍</strong> ' . $suministro['colonia'] . '</p>';
            echo '<p><strong>🏷️</strong> Marca: ' . $suministro['marca'] . '</p>';
            echo '<p style="margin-top:0.5rem; font-size: 0.75rem; color: var(--text-muted);">👤 ' . $suministro['voluntario'] . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p style="text-align: center; color: var(--text-muted); padding: 2rem 0; font-size: 0.85rem; font-style: italic;">Sin suministros</p>';
    }
    echo '</div>';
}

echo '            </div>';

if ($id_rol == 3) { // Solo mostrar el botón "+ Nuevo Suministro" si el usuario es Responsable
    echo '<div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <a href="/BD2SQLITO/core/crear_tareas.php?id_grupo='.$id_grupo.'&Usuario='.$usuario.'&Contraseña='.$contraseña.'" class="btn" style="width: auto;">
                + Nuevo Suministro
            </a>
          </div>';
}

echo '          <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <a href="/BD2SQLITO/core/principal.php?Usuario='.$usuario.'&Contraseña='.$contraseña.'" class="btn btn-secondary" style="width: auto;">
                    Ir al panel central
                </a>
            </div>
        </div>
    </div>
</body>
</html>';

mysqli_close($con);
?>