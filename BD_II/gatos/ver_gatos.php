<?php
// ver_gatos.php - listado de gatos con sidebar unificado
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// obtener id_grupo, id_rol e id_ayuntamiento del usuario logueado
$query_user = "SELECT id_grupo, id_rol, id_ayuntamiento
               FROM Persona
               WHERE usuario = '$usuario' AND contraseña = '$contraseña'
               LIMIT 1";

$res_user = mysqli_query($con, $query_user);
if (!$res_user || mysqli_num_rows($res_user) == 0) {
    die("Error: usuario/contraseña inválidos o no encontrados.");
}

$row_user = mysqli_fetch_assoc($res_user);
$id_grupo = $row_user['id_grupo'] ?? 0;
$id_rol   = $row_user['id_rol'] ?? 0;
$id_ayuntamiento = (int)($row_user['id_ayuntamiento'] ?? 0);

// consulta de gatos SOLO del ayuntamiento del usuario (vía colonia)
$consulta_gatos = "SELECT g.id, g.num_chip, g.descripción, g.foto, c.descripción AS colonia_actual
                  FROM Gato g
                  JOIN Colonia c ON g.id_colonia = c.id
                  WHERE c.id_ayuntamiento = $id_ayuntamiento
                  ORDER BY g.id";
$resultado_gatos = mysqli_query($con, $consulta_gatos);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Gatos | Sqlito</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-nav">
                <a href="/BD2SQLITO/core/principal.php?Usuario=<?=$usuario?>&Contraseña=<?=$contraseña?>" class="sidebar-logo">SQLITO</a>
                <?php
                //consulta para los permisos de la sidebar
                $consulta_perm = "SELECT titulo, enlace
                                  FROM persona
                                  JOIN rol ON persona.id_rol = rol.id
                                  JOIN puede_hacer ON puede_hacer.id_rol = rol.id
                                  JOIN privilegio ON privilegio.id = puede_hacer.id_privilegio
                                  WHERE persona.usuario = '$usuario'";
                $res_perm = mysqli_query($con, $consulta_perm);
                while ($reg_p = mysqli_fetch_assoc($res_perm)) {
                    $enlace = $reg_p["enlace"];
                    // algunos permisos necesitan el id_grupo para avanzar
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
            <h1>Gestión de Gatos</h1>
            <p class="welcome-text">Listado completo de felinos registrados en el sistema.</p>

            <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
                <?php if ($id_rol == 3): ?>
                    <a href="/BD2SQLITO/gatos/registrar_gato.php?Usuario=<?=$usuario?>&Contraseña=<?=$contraseña?>" class="btn">
                    + Registrar Nuevo Gato
                    </a>
                <?php endif; ?>
                <a href="/BD2SQLITO/gatos/ver_manada.php?Usuario=<?=$usuario?>&Contraseña=<?=$contraseña?>" class="btn btn-secondary">
                    Ver Manadas
                </a>
            </div>

            <div class="main-card">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nº Chip</th>
                                <th>Descripción</th>
                                <th>Foto</th>
                                <th>Colonia Actual</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($resultado_gatos) > 0) {
                                while ($g = mysqli_fetch_assoc($resultado_gatos)) {
                                    echo '<tr>
                                            <td><span class="badge">#'.$g['id'].'</span></td>
                                            <td><code style="font-size: 0.8rem;">'.($g['num_chip'] ?: 'N/A').'</code></td>
                                            <td style="max-width: 300px; font-size: 0.85rem;">'.nl2br(htmlspecialchars($g['descripción'])).'</td>
                                            <td>';
                                    if (!empty($g['foto'])) {
                                        echo '<img src="'.$g['foto'].'" alt="Foto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.5rem; border: 1px solid var(--border-color);">';
                                    } else {
                                        echo '<span style="color: var(--text-muted); font-size: 0.75rem;">Sin foto</span>';
                                    }
                                    echo '  </td>
                                            <td>
                                                <div style="display:flex; align-items:center; gap: 0.5rem;">
                                                    <span style="font-size: 1.1rem;"></span>
                                                    <span>'.($g['colonia_actual'] ?: '<span style="color:var(--text-muted)">Desconocida</span>').'</span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="/BD2SQLITO/gatos/historial_gato.php?id='.$g['id'].'&Usuario='.$usuario.'&Contraseña='.$contraseña.'" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Historial</a>
                                            </td>
                                          </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" style="text-align: center; padding: 4rem; color: var(--text-muted);">No hay gatos registrados para tu ayuntamiento.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <a href="/BD2SQLITO/core/principal.php?Usuario=<?=urlencode($usuario)?>&Contraseña=<?=urlencode($contraseña)?>" class="btn btn-secondary" style="margin-top: 1rem;">
                Volver al panel principal
            </a>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($con); ?>
