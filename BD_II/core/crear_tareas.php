<?php
$id_grupo = $_GET['id_grupo'];
$usuario = $_GET['Usuario'];
$contraseña = $_GET['Contraseña'];

$con = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($con, "BD2SQLITO");

// Obtener todas las colonias que pertenecen al ayuntamiento del grupo
$colonias_query = "SELECT c.id,
                          c.descripción 
                   FROM colonia c
                   JOIN grupo g 
                   ON g.id_ayuntamiento = c.id_ayuntamiento
                   AND g.id = $id_grupo";
$colonias_result = mysqli_query($con, $colonias_query);

// Obtener todos los voluntarios del grupo
$voluntarios_query = "SELECT id,
                             CONCAT(nombre, ' ', apellidos) AS nombre_completo 
                      FROM persona 
                      WHERE id_grupo = $id_grupo
                      AND id_rol = (SELECT id FROM rol WHERE rol = 'Voluntario')";
$voluntarios_result = mysqli_query($con, $voluntarios_query);

// Obtener todas las marcas
$marcas_query = "SELECT id, nombre FROM marca";
$marcas_result = mysqli_query($con, $marcas_query);

echo '<html>
        <head>
            <meta charset="utf-8">
            <title>Crear Tarea | Sqlito</title>
            <link rel="stylesheet" href="/BD2SQLITO/style.css">
        </head>
        <body>
            <div class="form-container">
                <h1>Crear Tarea</h1>
                <form action="/BD2SQLITO/core/guardar_tarea.php">
                    <!-- Descripción -->
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4" cols="50" required></textarea>
                    <br><br>

                    <!-- Fecha y Hora -->
                    <label for="fecha">Fecha y Hora:</label>
                    <input type="datetime-local" id="fecha" name="fecha" required>
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

                    <!-- Seleccionar voluntario -->
                    <label for="voluntario">Voluntario:</label>
                    <select id="voluntario" name="voluntario" required>
                        <option value="">Seleccione un voluntario</option>';
while ($voluntario = mysqli_fetch_assoc($voluntarios_result)) {
    echo '              <option value="'.$voluntario['id'].'">'.$voluntario['nombre_completo'].'</option>';
}
echo '              </select>
                    <br><br>

                    <!-- Seleccionar marca -->
                    <label for="marca">Marca:</label>
                    <select id="marca" name="marca" required>
                        <option value="">Seleccione una marca</option>';
while ($marca = mysqli_fetch_assoc($marcas_result)) {
    echo '              <option value="'.$marca['id'].'">'.$marca['nombre'].'</option>';
}
echo '              </select>
                    <br><br>

                    <!-- Botón para enviar -->
                    <input type="hidden" name="id_grupo" value="'.$id_grupo.'">
                    <input type="hidden" name="Usuario" value="'.$usuario.'">
                    <input type="hidden" name="Contraseña" value="'.$contraseña.'">
                    <input type="submit" value="Crear Tarea">
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