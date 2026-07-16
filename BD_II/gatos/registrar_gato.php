<?php
// registrar_gato.php - registro de nuevos gatos en una colonia
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// consulta para conseguir el ayuntamiento del usuario
$q_ayunt = "SELECT id_ayuntamiento
            FROM Persona
            WHERE usuario = '$usuario' AND contraseña = '$contraseña'
            LIMIT 1";

$r_user = mysqli_query($con, $q_ayunt);
if (!$r_user) {
    die("Error en consulta de ayuntamiento: " . mysqli_error($con) . "<br>SQL: " . $q_ayunt);
}
if (mysqli_num_rows($r_user) == 0) {
    die("Error: usuario/contraseña inválidos o no encontrados.");
}

$u = mysqli_fetch_assoc($r_user);
$id_ayuntamiento = (int)$u['id_ayuntamiento'];

$mensaje = null;
$error = null;

//si se ha dado a registrar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!$con) {
        $error = "Error al conectar con la base de datos: " . mysqli_connect_error();
    } elseif (!$db) {
        $error = "Error al seleccionar la base de datos: " . mysqli_error($con);
    } else {

        // recogida de input del usuario
        $num_chip_raw = isset($_POST['num_chip']) ? trim($_POST['num_chip']) : '';
        $descripcion_raw = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        $foto_raw = isset($_POST['foto']) ? trim($_POST['foto']) : '';
        $id_colonia = !empty($_POST['id_colonia']) ? intval($_POST['id_colonia']) : 0;
        
        // capa de seguridad
        $descripcion = mysqli_real_escape_string($con, $descripcion_raw);
        $num_chip = ($num_chip_raw !== '') ? mysqli_real_escape_string($con, $num_chip_raw) : null;
        $foto = ($foto_raw !== '') ? mysqli_real_escape_string($con, $foto_raw) : null;

        // si no se completan todos los datos necesários para el insert se comunica
        if ($descripcion_raw === '') {
            $error = "Error: La descripción es obligatoria.";
        } elseif ($id_colonia <= 0) {
            $error = "Error: Debes seleccionar una colonia.";
        } else {

            // validar que la colonia exista 
            $check = mysqli_query($con, "SELECT id FROM Colonia WHERE id = $id_colonia");
            if (!$check) {
                $error = "Error comprobando la colonia: " . mysqli_error($con);
            } elseif (mysqli_num_rows($check) == 0) {
                $error = "Error: La colonia seleccionada no existe (ID: $id_colonia).";
            } else {

                $num_chip_val = ($num_chip === null) ? "NULL" : "'" . $num_chip . "'";
                $foto_val     = ($foto === null) ? "NULL" : "'" . $foto . "'";

                // insert Gato
                $insert_gato = "INSERT INTO Gato (num_chip, `descripción`, foto, id_colonia)
                                VALUES ($num_chip_val, '$descripcion', $foto_val, $id_colonia)";

                $res_gato = mysqli_query($con, $insert_gato);
                if (!$res_gato) {
                    $error = "Error: " . mysqli_error($con) . "<br>SQL: " . $insert_gato;
                } else {
                    $id_gato = mysqli_insert_id($con);

                    if (!$id_gato || $id_gato <= 0) {
                        $error = "Error: No se obtuvo un ID válido del gato tras el insert.";
                    } else {

                        // insert inicial
                        $insert_albirament = "INSERT INTO Albirament (fecha, id_gato, id_colonia_albirada, id_colonia_anterior)
                                              VALUES (NOW(), $id_gato, $id_colonia, NULL)";
                        $res_alb = mysqli_query($con, $insert_albirament);

                        if (!$res_alb) {
                            $error = "Error: " . mysqli_error($con) . "<br>SQL: " . $insert_albirament;
                        } else {
                            $mensaje = "Gato registrado correctamente. (ID: $id_gato)";
                        }
                    }
                }
            }
        }
    }
}

// buscamos las colonias para el desplegable (solo las de nuestro ayuntamiento)
$colonias_res = mysqli_query($con, "SELECT id, `descripción` FROM Colonia WHERE id_ayuntamiento = '$id_ayuntamiento' ORDER BY id");

//html de la página
echo '<html>
<head>
    <meta charset="utf-8">
    <title>Registrar Gato</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Registrar Nuevo Gato</h1>';

if (!empty($error)) {
    echo '<p style="color:#ff4d4d; font-weight:bold;">'.$error.'</p>';
} elseif (!empty($mensaje)) {
    echo '<p style="color:green;">'.$mensaje.'</p>';
}

echo '  <form method="post">
            <label for="num_chip">Nº Chip (opcional):</label>
            <input type="text" id="num_chip" name="num_chip">
            <br><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
            <br><br>

            <label for="foto">Foto (ruta o URL, opcional):</label>
            <input type="text" id="foto" name="foto" placeholder="img/gatos/gatoX.jpg">
            <br><br>

            <label for="id_colonia">Manada / Colonia inicial (obligatorio):</label>
            <select id="id_colonia" name="id_colonia" required>
                <option value="">-- Selecciona una manada --</option>';

if ($colonias_res) {
    while ($c = mysqli_fetch_assoc($colonias_res)) {
        echo '      <option value="'.$c['id'].'">'.$c['id'].' - '.$c['descripción'].'</option>';
    }
}

echo '      </select>
            <br><br>

            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Registrar Gato">
        </form>
        <br>
        <form action="/BD2SQLITO/gatos/ver_gatos.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Volver al Listado">
        </form>
    </div>
</body>
</html>';

mysqli_close($con);
?>
