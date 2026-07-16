<?php
// crear_campana.php - Crear una nueva campaña
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $tipo = $_POST['tipo'];
    $tipo_vacuna = $_POST['tipo_vacuna'] ?: null;
    $id_responsable = $_POST['id_responsable'];
    $id_centro = $_POST['id_centro'];
    $id_colonia = $_POST['id_colonia'];

    if ($fecha_inicio > $fecha_fin) {
        $error = "La fecha de inicio no puede ser mayor que la fecha de fin.";
    } else {
        $insert_campana = "INSERT INTO Campaña (fecha_inicio, fecha_fin, tipo, tipo_vacunación, id_responsable, id_centro_veterinario, id_colonia)
                           VALUES ('$fecha_inicio', '$fecha_fin', '$tipo', '$tipo_vacuna', $id_responsable, $id_centro, $id_colonia)";
        mysqli_query($con, $insert_campana);
        $success = "Campaña creada correctamente.";
    }
}

// Cargar selects
$responsables_res = mysqli_query($con, "SELECT id, nombre, apellidos FROM Persona WHERE id_rol = 3"); // Responsable
$centros_res = mysqli_query($con, "SELECT id, nombre FROM Centro_veterinario");
$colonias_res = mysqli_query($con, "SELECT id, descripción FROM Colonia");

echo '<html>
<head>
    <meta charset="utf-8">
    <title>Crear Campaña</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
    <script>
        function mostrarVacuna() {
            const tipo = document.getElementById("tipo").value;
            document.getElementById("vacuna_div").style.display = (tipo === "Vacunación" ? "block" : "none");
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h1>Crear Nueva Campaña</h1>';

if (isset($success)) echo '<p style="color:green;">'.$success.'</p>';
if (isset($error)) echo '<p style="color:red;">'.$error.'</p>';

echo '  <form method="POST">
            <label for="fecha_inicio">Fecha inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            <br><br>

            <label for="fecha_fin">Fecha fin:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" required>
            <br><br>

            <label for="tipo">Tipo de campaña:</label>
            <select name="tipo" id="tipo" required onchange="mostrarVacuna()">
                <option value="Esterilización">Esterilización</option>
                <option value="Implementación chips">Implementación de chips</option>
                <option value="Vacunación">Vacunación</option>
            </select>
            <br><br>

            <div id="vacuna_div" style="display:none;">
                <label for="tipo_vacuna">Tipo de vacuna:</label>
                <input type="text" id="tipo_vacuna" name="tipo_vacuna">
                <br><br>
            </div>

            <label for="id_responsable">Responsable:</label>
            <select id="id_responsable" name="id_responsable" required>
                <option value="">Seleccione un responsable</option>';
while ($r = mysqli_fetch_assoc($responsables_res)) {
    echo '      <option value="'.$r['id'].'">'.$r['nombre'].' '.$r['apellidos'].'</option>';
}
echo '      </select>
            <br><br>

            <label for="id_centro">Centro Veterinario:</label>
            <select id="id_centro" name="id_centro" required>
                <option value="">Seleccione un centro</option>';
while ($c = mysqli_fetch_assoc($centros_res)) {
    echo '      <option value="'.$c['id'].'">'.$c['nombre'].'</option>';
}
echo '      </select>
            <br><br>

            <label for="id_colonia">Colonia:</label>
            <select id="id_colonia" name="id_colonia" required>
                <option value="">Seleccione una colonia</option>';
while ($co = mysqli_fetch_assoc($colonias_res)) {
    echo '      <option value="'.$co['id'].'">'.$co['descripción'].'</option>';
}
echo '      </select>
            <br><br>

            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Crear Campaña">
        </form>
        <br>
        <form action="/BD2SQLITO/campanas/ver_campanas_activas.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Volver a Campañas Activas">
        </form>
    </div>
</body>
</html>';

mysqli_close($con);
?>