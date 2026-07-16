<?php
// ver_manada.php - gestión de manadas con sidebar unificado
$id_colonia = isset($_GET['id_colonia']) ? intval($_GET['id_colonia']) : 0;
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

//consulta que devuelve los ids necesarios del usuario loggeado
$q_user = "SELECT id_grupo, id_rol, id_ayuntamiento
           FROM Persona
           WHERE usuario = '$usuario' AND contraseña = '$contraseña'
           LIMIT 1";

$r_user = mysqli_query($con, $q_user);
if (!$r_user || mysqli_num_rows($r_user) == 0) {
    die("Error: usuario/contraseña inválidos o no encontrados.");
}

$row = mysqli_fetch_assoc($r_user);

$id_grupo        = (int)($row['id_grupo'] ?? 0);
$id_rol          = (int)($row['id_rol'] ?? 0);
$id_ayuntamiento = (int)($row['id_ayuntamiento'] ?? 0);


// obtener colonias para selector
$resultado_colonias = mysqli_query($con, "SELECT id, descripción FROM Colonia WHERE id_ayuntamiento = '$id_ayuntamiento' ORDER BY id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manadas | Sqlito</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-nav">
                <a href="/BD2SQLITO/core/principal.php?Usuario=<?=urlencode($usuario)?>&Contraseña=<?=urlencode($contraseña)?>" class="sidebar-logo">SQLITO</a>
                <?php
                // consulta para obtener los permisos de la sidebar
                $consulta_perm = "SELECT titulo, enlace
                                  FROM persona
                                  JOIN rol ON persona.id_rol = rol.id
                                  JOIN puede_hacer ON puede_hacer.id_rol = rol.id
                                  JOIN privilegio ON privilegio.id = puede_hacer.id_privilegio
                                  WHERE persona.usuario = '$usuario'";
                $res_perm = mysqli_query($con, $consulta_perm);
                while ($reg_p = mysqli_fetch_assoc($res_perm)) {
                    $enlace = $reg_p["enlace"];
                    $extra = (strpos($enlace, 'id_grupo') === false) ? "?id_grupo=$id_grupo&" : "?";
                    echo '<a href="'.$enlace.$extra.'Usuario='.urlencode($usuario).'&Contraseña='.urlencode($contraseña).'">
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
            <h1>Gestión de Manadas</h1>
            <p class="welcome-text">Visualiza y gestiona las colonias felinas y sus habitantes actuales.</p>

            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; gap: 1.5rem; flex-wrap: wrap;">
                <form method="get" style="display: flex; gap: 1rem; align-items: flex-end; margin: 0; flex: 1;">
                    <div style="flex: 1; max-width: 400px;">
                        <label for="id_colonia">Seleccionar colonia / manada</label>
                        <select name="id_colonia" id="id_colonia" required style="margin-bottom: 0;">
                            <option value="">-- Elige una colonia --</option>
                            <?php
                            while ($c = mysqli_fetch_assoc($resultado_colonias)) {
                                $sel = ($id_colonia == $c['id']) ? 'selected' : '';
                                echo '<option value="'.$c['id'].'" '.$sel.'>#'.$c['id'].' - '.$c['descripción'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <input type="hidden" name="Usuario" value="<?=htmlspecialchars($usuario)?>">
                    <input type="hidden" name="Contraseña" value="<?=htmlspecialchars($contraseña)?>">
                    <button type="submit" class="btn btn-secondary" style="width: auto;">Consultar</button>
                </form>

                <?php if ($id_rol == 3): ?>
                    <a href="/BD2SQLITO/gatos/registrar_manada.php?Usuario=<?=urlencode($usuario)?>&Contraseña=<?=urlencode($contraseña)?>" class="btn" style="width: auto;">
                        + Nueva Manada
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($id_colonia > 0): ?>
                <div class="main-card">
                    <h3 style="margin-top: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 1.5rem;"></span> Habitando en colonia #<?=$id_colonia?>
                    </h3>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Gato</th>
                                    <th>Nº Chip</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // consulta donde se muestran los gatos de una colonia en específico
                                $consulta_gatos = "SELECT g.id, g.num_chip, g.`descripción`, g.foto
                                                FROM Gato g
                                                WHERE g.id_colonia = $id_colonia
                                                ORDER BY g.id";
                                $resultado_gatos = mysqli_query($con, $consulta_gatos);


                                if (mysqli_num_rows($resultado_gatos) == 0) {
                                    echo '<tr><td colspan="5" style="text-align: center; padding: 4rem; color: var(--text-muted);">No hay gatos registrados actualmente en esta colonia.</td></tr>';
                                } else {
                                    while ($g = mysqli_fetch_assoc($resultado_gatos)) {
                                        echo '<tr>
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: 1rem;">';
                                        if (!empty($g['foto'])) {
                                            echo '<img src="'.$g['foto'].'" alt="Foto" style="width: 45px; height: 45px; border-radius: 0.5rem; object-fit: cover;">';
                                        } else {
                                            echo '<div style="width: 45px; height: 45px; background: var(--bg-darker); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">🐱</div>';
                                        }
                                        echo '          <span class="badge">#'.$g['id'].'</span>
                                                    </div>
                                                </td>
                                                <td><code style="font-size: 0.8rem;">'.($g['num_chip'] ?: 'N/A').'</code></td>
                                                <td style="max-width: 300px; font-size: 0.85rem;">'.nl2br(htmlspecialchars($g['descripción'])).'</td>
                                                <td>
                                                    <a href="/BD2SQLITO/gatos/historial_gato.php?id='.$g['id'].'&Usuario='.urlencode($usuario).'&Contraseña='.urlencode($contraseña).'" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Historial</a>
                                                </td>
                                              </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="main-card" style="text-align: center; padding: 5rem;">
                    <span style="font-size: 4rem; display: block; margin-bottom: 1.5rem;"></span>
                    <h3 style="color: var(--text-main);">Consulta una manada</h3>
                    <p style="color: var(--text-muted);">Selecciona una colonia del menú superior para ver sus gatos actuales.</p>
                </div>
            <?php endif; ?>

            <a href="/BD2SQLITO/gatos/ver_gatos.php?Usuario=<?=urlencode($usuario)?>&Contraseña=<?=urlencode($contraseña)?>" class="btn btn-secondary" style="margin-top: 1rem; width: auto;">
                ← Volver al listado general
            </a>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($con); ?>
