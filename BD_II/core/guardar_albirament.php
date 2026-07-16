<?php

// Obtener parámetros
$colonia = $_GET['colonia'];
$id_grupo = $_GET['id_grupo'];
$usuario = $_GET['Usuario'];
$contraseña = $_GET['Contraseña'];
$id_gato = $_GET['gato'];

// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Obtener la colonia actual del gato
$query_colonia_actual = "SELECT id_colonia
                         FROM gato
                         WHERE id = $id_gato";
$result_colonia_actual = mysqli_query($con, $query_colonia_actual);
$row = mysqli_fetch_assoc($result_colonia_actual);
$id_colonia_anterior = $row['id_colonia'];

// Verificar si el gato ya pertenece a la colonia seleccionada
if ($id_colonia_anterior == $colonia) {
    echo '<html>
            <head>
                <meta charset="utf-8">
                <title>Avistamiento error | Sqlito</title>
                <link rel="stylesheet" href="/BD2SQLITO/style.css">
            </head>
            <body>
                <div class="form-container">
                    <h1>El gato ya pertenece a esta colonia</h1>
                    <p>No es necesario realizar ningún cambio.</p>
                    <form action="/BD2SQLITO/core/albirament.php">
                        <input type="hidden" name="id_grupo" value="'.$id_grupo.'">
                        <input type="hidden" name="Usuario" value="'.$usuario.'">
                        <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                        <input type="submit" value="Añadir más gatos">
                    </form>
                    <form action="/BD2SQLITO/core/principal.php">
                        <input type="hidden" name="Usuario" value="'.$usuario.'">
                        <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                        <input type="submit" value="Volver al Menú Principal">
                    </form>
                </div>
            </body>
          </html>';
} else {
    // Insertar un nuevo registro en la tabla Albirament
    $query_insert = "INSERT INTO Albirament (fecha, id_gato, id_colonia_albirada, id_colonia_anterior) VALUES
                         (NOW(), $id_gato, $colonia, $id_colonia_anterior)";
    $result_insert = mysqli_query($con, $query_insert);

    // Mensaje de resultado
    echo '<html>
            <head>
                <meta charset="utf-8">
                <title>Avistamiento guardado | Sqlito</title>
                <link rel="stylesheet" href="/BD2SQLITO/style.css">
            </head>
            <body>
                <div class="form-container">
                    <h1>Gato asignado con éxito</h1>
                    <p>El gato ha sido asignado correctamente a la colonia seleccionada.</p>
                    <form action="/BD2SQLITO/core/albirament.php">
                        <input type="hidden" name="id_grupo" value="'.$id_grupo.'">
                        <input type="hidden" name="Usuario" value="'.$usuario.'">
                        <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                        <input type="submit" value="Añadir más gatos">
                    </form>
                    <form action="/BD2SQLITO/core/principal.php">
                        <input type="hidden" name="Usuario" value="'.$usuario.'">
                        <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                        <input type="submit" value="Volver al Menú Principal">
                    </form>
                </div>
            </body>
          </html>';
}

mysqli_close($con);
?>