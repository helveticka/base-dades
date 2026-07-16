<?php
// Obtener los datos enviados desde el formulario
$comentario = $_GET['comentario'];
$fecha = $_GET['fecha'];
$colonia = $_GET['colonia'];
$responsable = $_GET['responsable'];

$id_grupo = $_GET['id_grupo'];
$usuario = $_GET['Usuario'];
$contraseña = $_GET['Contraseña'];

// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Insertar la tarea en la base de datos
$query = "INSERT INTO visita (fecha, comentario, id_responsable, id_colonia) VALUES
              ('$fecha', '$comentario', $responsable, $colonia)";
$result = mysqli_query($con, $query);

// Mostrar un mensaje dependiendo del resultado
echo '<html>
        <head>
            <meta charset="utf-8">
            <title>Visita Guardada | Sqlito</title>
            <link rel="stylesheet" href="/BD2SQLITO/style.css">
        </head>
        <body>
            <div class="form-container">
                <h1>Visita añadida con éxito</h1>
                <p>La visita ha sido guardada correctamente en la base de datos.</p>
                <form action="/BD2SQLITO/core/añadir_visita.php">
                    <input type="hidden" name="id_grupo" value="'.$id_grupo.'">
                    <input type="hidden" name="Usuario" value="'.$usuario.'">
                    <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                    <input type="submit" value="Volver a Añadir Visitas">
                </form>
                <form action="/BD2SQLITO/core/principal.php">
                    <input type="hidden" name="Usuario" value="'.$usuario.'">
                    <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                    <input type="submit" value="Volver al Menú Principal">
                </form>
            </div>
        </body>
        </html>';

mysqli_close($con);
?>