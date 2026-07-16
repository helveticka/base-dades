<?php
// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Obtener los datos enviados desde el formulario
$descripcion = $_GET['descripcion'];
$fecha = $_GET['fecha'];
$colonia = $_GET['colonia'];
$voluntario = $_GET['voluntario'];
$marca = $_GET['marca'];

$id_grupo = $_GET['id_grupo'];
$usuario = $_GET['Usuario'];
$contraseña = $_GET['Contraseña'];

// Obtener el valor de kg_por_gato de la marca seleccionada
$marca_query = "SELECT kg_por_gato 
                FROM marca
                WHERE id = $marca";
$marca_result = mysqli_query($con, $marca_query);
$marca_row = mysqli_fetch_assoc($marca_result);
$kg_por_gato = $marca_row['kg_por_gato'];

// Obtener el número de gatos en la colonia seleccionada
$gatos_query = "SELECT COUNT(*) AS num_gatos
                FROM Gato
                WHERE id_colonia = $colonia";
$gatos_result = mysqli_query($con, $gatos_query);
$gatos_row = mysqli_fetch_assoc($gatos_result);
$num_gatos = $gatos_row['num_gatos'];

// Calcular la cantidad
$cantidad = $kg_por_gato * $num_gatos;

// Insertar la tarea en la base de datos
$query = "INSERT INTO subministrament (fecha, descripción, cantidad, id_colonia, id_voluntario, id_marca) VALUES
              ('$fecha', '$descripcion', $cantidad, $colonia, $voluntario, $marca)";
$result = mysqli_query($con, $query);

// Mostrar un mensaje dependiendo del resultado
echo '<html>
        <head>
            <meta charset="utf-8">
            <title>Tarea Guardada | Sqlito</title>
            <link rel="stylesheet" href="/BD2SQLITO/style.css">
        </head>
        <body>
            <div class="form-container">
                <h1>Tarea creada con éxito</h1>
                <p>La tarea ha sido guardada correctamente en la base de datos.</p>
                <form action="/BD2SQLITO/core/crear_tareas.php">
                    <input type="hidden" name="id_grupo" value="'.$id_grupo.'">
                    <input type="hidden" name="Usuario" value="'.$usuario.'">
                    <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                    <input type="submit" value="Volver a Crear Tareas">
                </form>
                <form action="/BD2SQLITO/core/principal.php">
                    <input type="hidden" name="Usuario" value="'.$usuario.'">
                    <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                    <input type="submit" value="Volver al Menú Principal">
                </form>
            </div>
        </body>
        </html>';

// Cerrar la conexión
mysqli_close($con);
?>