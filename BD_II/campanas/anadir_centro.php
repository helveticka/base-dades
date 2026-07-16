<?php
// anadir_centro.php - Añadir un nuevo centro veterinario
$usuario = isset($_GET['Usuario']) ? $_GET['Usuario'] : '';
$contraseña = isset($_GET['Contraseña']) ? $_GET['Contraseña'] : '';

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($con, trim($_POST['nombre']));
    $telefono = mysqli_real_escape_string($con, trim($_POST['telefono']));
    $correo = mysqli_real_escape_string($con, trim($_POST['correo']));

    if ($nombre === "") {
        $error = "El nombre es obligatorio.";
    } else {
        $insert_centro = "INSERT INTO Centro_veterinario (nombre, telefono, correo)
                          VALUES ('$nombre', '$telefono', '$correo')";
        mysqli_query($con, $insert_centro);
        $success = "Centro veterinario añadido correctamente.";
    }
}

echo '<html>
<head>
    <meta charset="utf-8">
    <title>Añadir Centro Veterinario</title>
    <link rel="stylesheet" href="/BD2SQLITO/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Añadir Nuevo Centro Veterinario</h1>';

if (isset($success)) echo '<div class="msg-ok">'.$success.'</div>';
if (isset($error)) echo '<div class="msg-error">'.$error.'</div>';

echo '  <form method="POST">
            <label for="nombre"></span> Nombre del centro:</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Ej: Clínica VetSure">
            
            <label for="telefono"></span> Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" placeholder="Ej: 934 567 890">
            
            <label for="correo"></span> Correo electrónico:</label>
            <input type="email" id="correo" name="correo" placeholder="Ej: contacto@clinica.com">
            
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Añadir Centro">
        </form>
        <br>
        <form action="/BD2SQLITO/core/principal.php" method="get">
            <input type="hidden" name="Usuario" value="'.$usuario.'">
            <input type="hidden" name="Contraseña" value="'.$contraseña.'">
            <input type="submit" value="Volver a pantalla principal" class="btn-secondary">
        </form>
    </div>
</body>
</html>';

mysqli_close($con);
?>