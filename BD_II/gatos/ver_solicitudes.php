<?php
// ver_solicitudes.php - ver solicitudes de retirada del usuario loggeado
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

//consulta para obtener los ids necesarios de la persona loggeada
$q_user = "SELECT id, id_grupo
           FROM Persona
           WHERE usuario = '$usuario' AND contraseña = '$contraseña'
           LIMIT 1";

$r_user = mysqli_query($con, $q_user);
if (!$r_user || mysqli_num_rows($r_user) == 0) {
    die("Error: usuario/contraseña inválidos o no encontrados.");
}

$row = mysqli_fetch_assoc($r_user);

$id       = (int)$row['id'];
$id_grupo = (int)$row['id_grupo'];

//array de estados posibles
$estados_posibles = ['Pendiente', 'Aprobada', 'Rechazada', 'Completada'];

//consulta para obtener las solicitudes que ha realizado la persona loggeada
$consulta_sol = "SELECT s.id, s.fecha, s.estado, g.id AS id_gato, g.num_chip, g.descripción AS gato_desc,
                        p.nombre AS resp_nombre, p.apellidos AS resp_apellidos,
                        c.nombre AS cementerio_nombre
                 FROM Solicitud_retirada s
                 JOIN Gato g ON s.id_gato = g.id
                 JOIN Persona p ON s.id_responsable = p.id
                 LEFT JOIN Cementerio c ON s.id_cementerio = c.id
                 WHERE s.id_responsable = $id";

// condición del filtro
if ($estado_filtro && in_array($estado_filtro, $estados_posibles)) {
    $estado_esc = mysqli_real_escape_string($con, $estado_filtro);
    $consulta_sol .= " AND s.estado = '$estado_esc'";
}

$consulta_sol .= " ORDER BY s.fecha DESC";
$resultado_sol = mysqli_query($con, $consulta_sol);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Solicitudes de Retirada | Sqlito</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
    <style>
        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pendiente { background: #fef3c7; color: #92400e; }
        .status-aprobada { background: #dcfce7; color: #166534; }
        .status-rechazada { background: #fee2e2; color: #991b1b; }
        .status-completada { background: #e0e7ff; color: #3730a3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-nav">
                <a href="/BD2SQLITO/core/principal.php?Usuario=<?=$usuario?>&Contraseña=<?=$contraseña?>" class="sidebar-logo">SQLITO</a>
                <?php
                //consulta de los permisos para la sidebar
                $consulta_perm = "SELECT titulo, enlace
                                  FROM persona
                                  JOIN rol ON persona.id_rol = rol.id
                                  JOIN puede_hacer ON puede_hacer.id_rol = rol.id
                                  JOIN privilegio ON privilegio.id = puede_hacer.id_privilegio
                                  WHERE persona.usuario = '$usuario'";
                $res_perm = mysqli_query($con, $consulta_perm);
                while ($reg_p = mysqli_fetch_assoc($res_perm)) {
                    $enlace = $reg_p["enlace"];
                    //algunos permisos necesitan el grupo del usuario
                    $extra = (strpos($enlace, 'id_grupo') === false) ? "?id_grupo=$id_grupo&" : "?";
                    echo '<a href="'.$enlace.$extra.'Usuario='.$usuario.'&Contraseña='.$contraseña.'">
                            <span>'.$reg_p["titulo"].'</span>
                          </a>';
                }
                ?>
            </div>
            <div class="sidebar-footer">
                <form action="/BD2SQLITO/core/login.html">
                    <input type="submit" value="Cerrar sesión" class="btn-danger">
                </form>
            </div>
        </div>

        <div class="main-content">
            <h1>Solicitudes de Retirada</h1>
            <p class="welcome-text">Gestión de traslados y retiradas de felinos a cementerios o centros.</p>

            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; gap: 1rem; flex-wrap: wrap;">
                <form method="get" style="display: flex; gap: 1rem; align-items: flex-end; margin: 0;">
                    <div>
                        <label for="estado">Filtrar por estado</label>
                        <select name="estado" id="estado" style="margin-bottom: 0;">
                            <option value="">-- Todos los estados --</option>
                            <?php
                            foreach ($estados_posibles as $e) {
                                $sel = ($estado_filtro === $e) ? 'selected' : '';
                                echo '<option value="'.$e.'" '.$sel.'>'.$e.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <input type="hidden" name="Usuario" value="<?=htmlspecialchars($usuario)?>">
                    <input type="hidden" name="Contraseña" value="<?=htmlspecialchars($contraseña)?>">
                    <button type="submit" class="btn btn-secondary" style="width: auto;">Filtrar</button>
                </form>

                <a href="/BD2SQLITO/gatos/solicitud_retirada.php?Usuario=<?=urlencode($usuario)?>&Contraseña=<?=urlencode($contraseña)?>" class="btn" style="width: auto;">
                    + Nueva Solicitud
                </a>
            </div>

            <div class="main-card">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Información del Gato</th>
                                <th>Responsable</th>
                                <th>Ubicación Destino</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($resultado_sol) > 0) {
                                while ($s = mysqli_fetch_assoc($resultado_sol)) {
                                    $statusClass = 'status-' . strtolower($s['estado']);
                                    echo '<tr>
                                            <td><span class="badge">#'.$s['id'].'</span></td>
                                            <td style="font-size: 0.85rem; color: var(--text-muted);"> '.date('d/m/Y', strtotime($s['fecha'])).'</td>
                                            <td><span class="status-badge '.$statusClass.'">'.$s['estado'].'</span></td>
                                            <td>
                                                <div style="font-weight: 600;"> Gato #'.$s['id_gato'].'</div>
                                                <div style="font-size: 0.8rem; color: var(--text-muted);">Chip: '.($s['num_chip'] ?: 'N/A').'</div>
                                                <div style="font-size: 0.75rem; font-style: italic; color: var(--text-muted); max-width: 200px;">'.htmlspecialchars(substr($s['gato_desc'], 0, 50)).'...</div>
                                            </td>
                                            <td>
                                                <div style="display:flex; align-items:center; gap: 0.5rem;">
                                                    <span style="font-size: 1.1rem;"></span>
                                                    <span>'.$s['resp_nombre'].' '.$s['resp_apellidos'].'</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display:flex; align-items:center; gap: 0.5rem;">
                                                    <span style="font-size: 1.1rem;"></span>
                                                    <span>'.($s['cementerio_nombre'] ?: '<span style="color:var(--text-muted)">Pendiente</span>').'</span>
                                                </div>
                                            </td>
                                          </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" style="text-align:center; padding: 4rem; color: var(--text-muted);">No se encontraron solicitudes con este filtro.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <a href="/BD2SQLITO/core/principal.php?Usuario=<?=urlencode($usuario)?>&Contraseña=<?=urlencode($contraseña)?>" class="btn btn-secondary" style="margin-top: 1rem; width: auto;">
                Volver al panel principal
            </a>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($con); ?>