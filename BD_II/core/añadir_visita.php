<?php
$id_grupo = $_GET['id_grupo'];
$usuario = $_GET['Usuario'];
$contraseña = $_GET['Contraseña'];

// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Obtener el ID del responsable
$responsable_query = "SELECT id 
                      FROM persona 
                      WHERE usuario = '$usuario'";
$responsable_result = mysqli_query($con, $responsable_query);
$responsable_row = mysqli_fetch_assoc($responsable_result);
$id_responsable = $responsable_row['id'];

// Obtener las colonias asociadas al grupo
$colonias_query = "SELECT c.id,
                          c.descripción 
                   FROM colonia c
                   JOIN grupo g 
                   ON g.id_ayuntamiento = c.id_ayuntamiento
                   AND g.id = $id_grupo";
$colonias_result = mysqli_query($con, $colonias_query);

// Obtener la fecha y hora actual en el formato requerido por datetime-local
$fecha_actual = date('Y-m-d\TH:i');

echo '<html>
        <head>
            <meta charset="utf-8">
            <title>Añadir Visita | Sqlito</title>
            <link rel="stylesheet" href="/BD2SQLITO/style.css">
        </head>
        <body>
            <div class="form-container">
                <h1>Añadir Visita</h1>
                <form action="/BD2SQLITO/core/guardar_visita.php">
                    <!-- Fecha y Hora -->
                    <label for="fecha">Fecha y Hora:</label>
                    <input type="datetime-local" id="fecha" name="fecha" value="' . $fecha_actual . '" required>
                    <br><br>

                    <!-- Comentario -->
                    <label for="comentario">Comentario:</label>
                    <textarea id="comentario" name="comentario" rows="4" cols="50" required></textarea>
                    <br><br>

                    <!-- Seleccionar colonia -->
                    <label for="colonia">Colonia:</label>
                    <select id="colonia" name="colonia" required>
                        <option value="">Seleccione una colonia</option>';
while ($colonia = mysqli_fetch_assoc($colonias_result)) {
    echo '              <option value="'.$colonia['id'].'">'.$colonia['descripción'].'</option>';
}
echo '              </select>
                    <br><br>

                    <!-- Botón para enviar -->
                    <input type="hidden" name="responsable" value="'.$id_responsable.'">
                    <input type="hidden" name="id_grupo" value="'.$id_grupo.'">
                    <input type="hidden" name="Usuario" value="'.$usuario.'">
                    <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                    <input type="submit" value="Añadir Visita">
                </form>
                <!-- Botón para volver al menú principal -->
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